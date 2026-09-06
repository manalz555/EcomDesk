<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Company;
use App\Models\CustomField;
use Illuminate\Http\Request;

/**
 * CRM: the customer directory. Read access is open to any staff member;
 * edit/delete is admin-only (see authorizeAdmin()) — agents can only create
 * a client on the fly from the "new conversation" form.
 */
class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::query()
            ->when($request->q, function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->q}%")
                  ->orWhere('prenom', 'like', "%{$request->q}%")
                  ->orWhere('email', 'like', "%{$request->q}%");
            })
            ->with('company')
            ->withCount('conversations')
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create', [
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'whatsapp_id' => ['nullable', 'string', 'max:255'],
            'instagram_handle' => ['nullable', 'string', 'max:255'],
            'messenger_psid' => ['nullable', 'string', 'max:255'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
        ]);

        $client = Client::create($data);

        AuditLog::record('client.created', $client, "Client créé : {$client->nom_complet}");

        return redirect()->route('clients.show', $client)->with('success', 'Client cree avec succes.');
    }

    public function show(Client $client)
    {
        $client->load([
            'company',
            'conversations' => fn ($q) => $q->latest(),
            'customFieldValues.customField',
        ]);

        $customFields = CustomField::orderBy('label')->get();

        return view('clients.show', compact('client', 'customFields'));
    }

    public function edit(Client $client)
    {
        $this->authorizeAdmin();

        return view('clients.edit', [
            'client' => $client,
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'whatsapp_id' => ['nullable', 'string', 'max:255'],
            'instagram_handle' => ['nullable', 'string', 'max:255'],
            'messenger_psid' => ['nullable', 'string', 'max:255'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($data);

        AuditLog::record('client.updated', $client, "Client mis à jour : {$client->nom_complet}");

        return redirect()->route('clients.show', $client)->with('success', 'Client mis a jour.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeAdmin();

        $nomComplet = $client->nom_complet;

        $client->delete();

        AuditLog::record('client.deleted', description: "Client supprimé : {$nomComplet}");

        return redirect()->route('clients.index')->with('success', 'Client supprime.');
    }

    public function updateCustomFields(Request $request, Client $client)
    {
        $data = $request->validate([
            'values' => ['array'],
            'values.*' => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data['values'] ?? [] as $customFieldId => $value) {
            $client->customFieldValues()->updateOrCreate(
                ['custom_field_id' => $customFieldId],
                ['value' => $value]
            );
        }

        AuditLog::record('client.custom_fields_updated', $client, "Champs personnalisés mis à jour : {$client->nom_complet}");

        return back()->with('success', 'Champs personnalisés mis à jour.');
    }

    /** Seul un administrateur peut modifier/supprimer une fiche client (RG). */
    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403, "Seul l'administrateur peut modifier ou supprimer un client.");
    }
}
