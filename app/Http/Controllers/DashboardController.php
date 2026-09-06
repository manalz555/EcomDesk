<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Conversation;

/** Home page after login: the at-a-glance counters and recent-activity feed. */
class DashboardController extends Controller
{
    public function index()
    {
        // Simple counts, not cached: unlike AnalyticsController (heavier
        // aggregations), these are cheap enough to compute on every request.
        $totalClients = Client::count();
        $totalConversations = Conversation::count();
        $enAttente = Conversation::where('statut', 'en_attente')->count();
        $resolues = Conversation::where('statut', 'resolu')->count();
        $nouvelles = Conversation::where('statut', 'nouveau')->count();
        $enCours = Conversation::where('statut', 'en_cours')->count();

        $parCategorie = Conversation::selectRaw('categorie, count(*) as total')
            ->groupBy('categorie')
            ->pluck('total', 'categorie');

        $dernieresConversations = Conversation::with(['client', 'agent'])
            ->latest()
            ->take(8)
            ->get();

        $activites = AuditLog::with('actor')
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'totalClients',
            'totalConversations',
            'enAttente',
            'resolues',
            'nouvelles',
            'enCours',
            'parCategorie',
            'dernieresConversations',
            'activites',
        ));
    }
}
