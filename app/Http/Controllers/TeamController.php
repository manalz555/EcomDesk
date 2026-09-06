<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

/** CRUD for Team + its member roster. Reserve aux administrateurs et managers (cf. middleware "manage-team"). */
class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount(['users', 'conversations'])->orderBy('name')->paginate(15);

        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create', [
            'members' => User::where('is_bot', false)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'members' => ['array'],
            'members.*' => ['exists:users,id'],
        ]);

        $team = Team::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $team->users()->sync($data['members'] ?? []);

        AuditLog::record('team.created', $team, "Équipe créée : {$team->name}");

        return redirect()->route('teams.index')->with('success', 'Équipe créée avec succès.');
    }

    public function edit(Team $team)
    {
        $team->load('users');

        return view('teams.edit', [
            'team' => $team,
            'members' => User::where('is_bot', false)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Team $team)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'members' => ['array'],
            'members.*' => ['exists:users,id'],
        ]);

        $team->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $team->users()->sync($data['members'] ?? []);

        AuditLog::record('team.updated', $team, "Équipe mise à jour : {$team->name}");

        return redirect()->route('teams.index')->with('success', 'Équipe mise à jour.');
    }

    public function destroy(Team $team)
    {
        $name = $team->name;

        $team->delete();

        AuditLog::record('team.deleted', description: "Équipe supprimée : {$name}");

        return redirect()->route('teams.index')->with('success', 'Équipe supprimée.');
    }
}
