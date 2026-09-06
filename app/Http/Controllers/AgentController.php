<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/** Staff account management (create/edit role & active status). Reserve a l'administrateur (cf. middleware "admin" applique dans routes/web.php). */
class AgentController extends Controller
{
    public function index()
    {
        $agents = User::where('is_bot', false)
            ->whereIn('role', User::ROLES)
            ->withCount('conversations')
            ->orderBy('name')
            ->paginate(15);

        return view('agents.index', compact('agents'));
    }

    public function create()
    {
        return view('agents.create', ['roles' => User::ROLES]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $agent = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'actif' => true,
            // Un compte cree par un administrateur est deja verifie : pas besoin de confirmer l'email.
            'email_verified_at' => now(),
        ]);

        AuditLog::record('agent.created', $agent, "Compte {$agent->role} créé : {$agent->name}");

        return redirect()->route('agents.index')->with('success', 'Compte créé avec succès.');
    }

    public function edit(User $agent)
    {
        return view('agents.edit', ['agent' => $agent, 'roles' => User::ROLES]);
    }

    public function update(Request $request, User $agent)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$agent->id],
            'role' => ['required', Rule::in(User::ROLES)],
            'actif' => ['required', 'boolean'],
        ]);

        // Self-lockout guard: without this, an admin editing their own
        // account could demote or deactivate themselves and lose access to
        // the very page needed to undo it.
        if ($agent->id === auth()->id() && ($data['role'] !== 'admin' || ! $data['actif'])) {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer vos propres droits administrateur ni désactiver votre propre compte.']);
        }

        $wasActive = $agent->actif;
        $previousRole = $agent->role;

        $agent->update($data);

        if ($wasActive && ! $agent->actif) {
            AuditLog::record('agent.deactivated', $agent, "Compte désactivé : {$agent->name}");
        } elseif (! $wasActive && $agent->actif) {
            AuditLog::record('agent.reactivated', $agent, "Compte réactivé : {$agent->name}");
        } elseif ($previousRole !== $agent->role) {
            AuditLog::record('agent.role_changed', $agent, "Rôle de {$agent->name} changé : {$previousRole} → {$agent->role}");
        } else {
            AuditLog::record('agent.updated', $agent, "Compte mis à jour : {$agent->name}");
        }

        return redirect()->route('agents.index')->with('success', 'Compte mis a jour.');
    }
}
