<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\WorkspaceSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Workspace-wide branding/config, editing the single WorkspaceSetting row.
 * Reserve a l'administrateur (cf. middleware "admin" applique dans routes/web.php).
 */
class SettingsController extends Controller
{
    private const JOURS = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

    public function edit()
    {
        return view('settings.edit', [
            'settings' => WorkspaceSetting::current(),
            'jours' => self::JOURS,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            // "image" seul autorise le SVG (risque XSS si affiche inline) : on restreint aux formats matriciels.
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'default_theme' => ['required', 'in:light,dark,system'],
            'business_hours' => ['nullable', 'array'],
            'business_hours.*.enabled' => ['nullable', 'boolean'],
            'business_hours.*.start' => ['nullable', 'string'],
            'business_hours.*.end' => ['nullable', 'string'],
        ]);

        $settings = WorkspaceSetting::current();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        // Rebuilt from scratch (not merged) so every day always has a
        // consistent enabled/start/end shape, even for days the form
        // submitted nothing for (an unchecked checkbox sends no value at all).
        $businessHours = [];
        foreach (self::JOURS as $jour) {
            $businessHours[$jour] = [
                'enabled' => $request->boolean("business_hours.$jour.enabled"),
                'start' => $request->input("business_hours.$jour.start", '09:00'),
                'end' => $request->input("business_hours.$jour.end", '18:00'),
            ];
        }

        $settings->update([
            'company_name' => $data['company_name'],
            'default_theme' => $data['default_theme'],
            'logo_path' => $data['logo_path'] ?? $settings->logo_path,
            'business_hours' => $businessHours,
        ]);

        AuditLog::record('settings.updated', $settings, 'Paramètres de l\'espace de travail mis à jour.');

        return back()->with('success', 'Paramètres enregistrés.');
    }
}
