<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Http\Request;

/** CRUD for the businesses that Client records can optionally belong to. Reserve a l'administrateur (cf. middleware "admin" applique dans routes/web.php). */
class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->withCount('clients')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $company = Company::create($data);

        AuditLog::record('company.created', $company, "Entreprise créée : {$company->name}");

        return redirect()->route('companies.index')->with('success', 'Entreprise créée avec succès.');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        $company->update($data);

        AuditLog::record('company.updated', $company, "Entreprise mise à jour : {$company->name}");

        return redirect()->route('companies.index')->with('success', 'Entreprise mise à jour.');
    }

    public function destroy(Company $company)
    {
        $name = $company->name;

        $company->delete();

        AuditLog::record('company.deleted', description: "Entreprise supprimée : {$name}");

        return redirect()->route('companies.index')->with('success', 'Entreprise supprimée.');
    }
}
