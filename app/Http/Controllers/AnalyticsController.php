<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/** The "Analytique" page (Manager+): response-time, resolution and satisfaction metrics, computed with raw aggregate SQL rather than loading everything into PHP. */
class AnalyticsController extends Controller
{
    public function index()
    {
        // Les agregations sont couteuses (plusieurs jointures groupees) : mises en cache 60s,
        // largement suffisant pour un tableau de bord consulte frequemment sans etre temps reel.
        $data = Cache::remember('analytics.dashboard', 60, function () {
            $totalConversations = Conversation::count();
            $resolues = Conversation::where('statut', 'resolu')->count();
            $tauxResolution = $totalConversations ? round($resolues / $totalConversations * 100) : 0;

            $satisfactionMoyenne = Conversation::whereNotNull('satisfaction')->avg('satisfaction');
            $nombreAvis = Conversation::whereNotNull('satisfaction')->count();

            $tempsReponseMoyen = $this->averageFirstResponseMinutes();

            $parCanal = Conversation::selectRaw('canal, count(*) as total')->groupBy('canal')->pluck('total', 'canal');
            $parCategorie = Conversation::selectRaw('categorie, count(*) as total')->groupBy('categorie')->pluck('total', 'categorie');
            $parStatut = Conversation::selectRaw('statut, count(*) as total')->groupBy('statut')->pluck('total', 'statut');

            $volumeParJour = $this->volumeByDay(14);

            $performanceParAgent = $this->agentPerformance();

            return compact(
                'totalConversations',
                'resolues',
                'tauxResolution',
                'satisfactionMoyenne',
                'nombreAvis',
                'tempsReponseMoyen',
                'parCanal',
                'parCategorie',
                'parStatut',
                'volumeParJour',
                'performanceParAgent',
            );
        });

        return view('analytics.index', $data);
    }

    // "First response time" isn't a stored column — it's derived by joining
    // each conversation to the earliest reply in its own reponses, then
    // averaging the gap in minutes. Doing this in SQL avoids pulling every
    // conversation+reponse pair into PHP just to compute a single average.
    private function averageFirstResponseMinutes(): ?float
    {
        $value = DB::table('conversations as c')
            ->joinSub(
                DB::table('reponses')->selectRaw('conversation_id, MIN(created_at) as first_reponse')->groupBy('conversation_id'),
                'first_r',
                'first_r.conversation_id',
                '=',
                'c.id'
            )
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, c.created_at, first_r.first_reponse)) as avg_minutes')
            ->value('avg_minutes');

        return $value !== null ? round((float) $value, 1) : null;
    }

    private function volumeByDay(int $jours): array
    {
        $depuis = Carbon::now()->subDays($jours - 1)->startOfDay();

        $rows = Conversation::selectRaw('DATE(created_at) as jour, count(*) as total')
            ->where('created_at', '>=', $depuis)
            ->groupBy('jour')
            ->pluck('total', 'jour');

        $result = [];
        for ($i = 0; $i < $jours; $i++) {
            $date = $depuis->copy()->addDays($i)->format('Y-m-d');
            $result[$date] = (int) ($rows[$date] ?? 0);
        }

        return $result;
    }

    private function agentPerformance()
    {
        return User::where('is_bot', false)
            ->whereIn('role', ['admin', 'manager', 'agent'])
            ->withCount(['conversations', 'reponses'])
            ->get()
            ->map(function (User $agent) {
                $resolues = Conversation::where('agent_id', $agent->id)->where('statut', 'resolu')->count();
                $satisfaction = Conversation::where('agent_id', $agent->id)->whereNotNull('satisfaction')->avg('satisfaction');

                $tempsReponse = DB::table('conversations as c')
                    ->joinSub(
                        DB::table('reponses')->selectRaw('conversation_id, agent_id, MIN(created_at) as first_reponse')->groupBy('conversation_id', 'agent_id'),
                        'first_r',
                        'first_r.conversation_id',
                        '=',
                        'c.id'
                    )
                    ->where('first_r.agent_id', $agent->id)
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, c.created_at, first_r.first_reponse)) as avg_minutes')
                    ->value('avg_minutes');

                return [
                    'agent' => $agent,
                    'conversations' => $agent->conversations_count,
                    'resolues' => $resolues,
                    'satisfaction' => $satisfaction ? round($satisfaction, 1) : null,
                    'temps_reponse' => $tempsReponse ? round($tempsReponse, 1) : null,
                ];
            })
            ->sortByDesc('conversations')
            ->values();
    }
}
