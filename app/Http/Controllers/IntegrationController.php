<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\AutomationRule;
use App\Models\ChannelIntegration;
use App\Models\Conversation;
use App\Models\User;
use App\Support\SqlDialect;
use Illuminate\Http\Request;

/**
 * Two responsibilities: (1) connect/disconnect external channel credentials
 * (stored encrypted on ChannelIntegration), and (2) CRUD the AutomationRule
 * rows that drive the AI draft-reply engine.
 * Reserve a l'administrateur (cf. middleware "admin" applique dans routes/web.php).
 */
class IntegrationController extends Controller
{
    // Static metadata (display label + form field name) for every channel —
    // drives both the settings loop below and the connect forms in the view.
    private const CHANNELS = [
        'whatsapp' => ['label' => 'WhatsApp Business API', 'field' => 'Token d\'accès'],
        'instagram' => ['label' => 'Instagram Graph API', 'field' => 'Token d\'accès'],
        'messenger' => ['label' => 'Facebook Messenger API', 'field' => 'Token de page'],
        'telegram' => ['label' => 'Telegram Bot API', 'field' => 'Token du bot'],
        'email' => ['label' => 'SMTP Email', 'field' => 'Mot de passe SMTP'],
        'openai' => ['label' => 'OpenAI (réponses automatisées)', 'field' => 'Clé API'],
        'ocr' => ['label' => 'OCR (extraction de texte des pièces jointes)', 'field' => 'Clé API'],
    ];

    public function index()
    {
        foreach (self::CHANNELS as $channel => $meta) {
            ChannelIntegration::firstOrCreate(['channel' => $channel]);
        }

        $integrations = ChannelIntegration::orderByRaw(
            SqlDialect::orderByValues('channel', array_keys(self::CHANNELS))
        )->get();

        $rules = AutomationRule::with('bot')->orderBy('name')->get();
        $bots = User::where('is_bot', true)->get();

        return view('integrations.index', [
            'integrations' => $integrations,
            'channels' => self::CHANNELS,
            'rules' => $rules,
            'bots' => $bots,
            'canaux' => Conversation::CANAUX,
            'categories' => Conversation::CATEGORIES,
        ]);
    }

    public function updateChannel(Request $request, ChannelIntegration $integration)
    {
        $data = $request->validate([
            'api_key' => ['nullable', 'string', 'max:500'],
            'webhook_token' => ['nullable', 'string', 'max:255'],
        ]);

        $isConnected = filled($data['api_key'] ?? null);

        $integration->update([
            'config' => ['api_key' => $data['api_key'] ?? null],
            'webhook_token' => $data['webhook_token'] ?? $integration->webhook_token,
            'is_connected' => $isConnected,
            'connected_at' => $isConnected ? now() : null,
        ]);

        AuditLog::record('integration.updated', $integration, "Intégration mise à jour : {$integration->channel}");

        return back()->with('success', 'Intégration mise à jour.');
    }

    public function disconnectChannel(ChannelIntegration $integration)
    {
        $integration->update(['config' => null, 'is_connected' => false, 'connected_at' => null]);

        AuditLog::record('integration.disconnected', $integration, "Intégration déconnectée : {$integration->channel}");

        return back()->with('success', 'Intégration déconnectée.');
    }

    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bot_user_id' => ['required', 'exists:users,id'],
            'canal' => ['nullable', 'in:'.implode(',', Conversation::CANAUX)],
            'categorie' => ['nullable', 'in:'.implode(',', Conversation::CATEGORIES)],
            'prompt_template' => ['nullable', 'string'],
        ]);

        // Created disabled by default: an admin must explicitly opt in via
        // toggleRule() before it can start generating drafts.
        $rule = AutomationRule::create([...$data, 'enabled' => false]);

        AuditLog::record('automation_rule.created', $rule, "Règle d'automatisation créée : {$rule->name}");

        return back()->with('success', 'Règle créée avec succès.');
    }

    public function toggleRule(AutomationRule $rule)
    {
        $rule->update(['enabled' => ! $rule->enabled]);

        AuditLog::record(
            $rule->enabled ? 'automation_rule.enabled' : 'automation_rule.disabled',
            $rule,
            ($rule->enabled ? 'Règle activée : ' : 'Règle désactivée : ').$rule->name
        );

        return back()->with('success', 'Règle mise à jour.');
    }

    public function destroyRule(AutomationRule $rule)
    {
        $name = $rule->name;
        $rule->delete();

        AuditLog::record('automation_rule.deleted', description: "Règle d'automatisation supprimée : {$name}");

        return back()->with('success', 'Règle supprimée.');
    }
}
