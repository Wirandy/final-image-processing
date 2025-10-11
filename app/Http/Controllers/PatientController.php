<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $patients = Patient::when($q, function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('identifier', 'like', "%{$q}%");
        })->orderByDesc('id')->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'q'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'identifier' => ['nullable', 'string', 'max:255'],
        ]);

        $patient = Patient::create($data);

        ActivityLog::create([
            'action' => 'patient.create',
            'user_id' => optional($request->user())->id,
            'patient_id' => $patient->id,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
        ]);

        return redirect()->route('patients.show', $patient)->with('status', 'Patient saved');
    }

    public function show(Patient $patient): View
    {
        $patient->load(['images' => function ($q) {
            $q->orderByDesc('id');
        }]);
        return view('patients.show', compact('patient'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $patient->update($data);

        ActivityLog::create([
            'action' => 'patient.update',
            'user_id' => optional($request->user())->id,
            'patient_id' => $patient->id,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
        ]);

        return redirect()->route('patients.show', $patient)->with('status', 'Notes updated successfully');
    }
}


