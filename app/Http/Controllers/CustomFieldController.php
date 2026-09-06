<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Lets an admin define extra per-client fields (see CustomField/CustomFieldValue) without a migration for each one. Reserve a l'administrateur (cf. middleware "admin" applique dans routes/web.php). */
class CustomFieldController extends Controller
{
    public function index()
    {
        $customFields = CustomField::orderBy('label')->get();

        return view('custom-fields.index', compact('customFields'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:text,number,date,select'],
            'options' => ['nullable', 'string'],
        ]);

        $customField = CustomField::create([
            'label' => $data['label'],
            'key' => Str::slug($data['label'], '_'),
            'type' => $data['type'],
            // "options" only makes sense for the "select" type — sent as a
            // comma-separated string from the form, split into an array here.
            'options' => $data['type'] === 'select' && $data['options']
                ? array_map('trim', explode(',', $data['options']))
                : null,
        ]);

        AuditLog::record('custom_field.created', $customField, "Champ personnalisé créé : {$customField->label}");

        return back()->with('success', 'Champ personnalisé créé.');
    }

    public function destroy(CustomField $customField)
    {
        $label = $customField->label;

        $customField->delete();

        AuditLog::record('custom_field.deleted', description: "Champ personnalisé supprimé : {$label}");

        return back()->with('success', 'Champ personnalisé supprimé.');
    }
}
