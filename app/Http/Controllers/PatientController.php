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
        ]);

        // Automatically set identifier from logged-in user's email
        $data['identifier'] = $request->user()->email ?? 'unknown';

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
            $q->orderByDesc('id')->limit(3);
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

    public function destroy(Request $request, Patient $patient): RedirectResponse
    {
        // Check if user owns this patient (identifier matches user email)
        if ($patient->identifier !== $request->user()->email) {
            return back()->with('error', 'You can only delete patients you created.');
        }

        // Delete all images and their files
        foreach ($patient->images as $image) {
            try {
                // Delete original file
                if ($image->original_path) {
                    \Storage::disk('public')->delete($image->original_path);
                }
                // Delete processed file
                if ($image->processed_path) {
                    \Storage::disk('public')->delete($image->processed_path);
                }
                // Delete all files in processing history
                if ($image->processing_history) {
                    foreach ($image->processing_history as $history) {
                        if (isset($history['path'])) {
                            \Storage::disk('public')->delete($history['path']);
                        }
                    }
                }
            } catch (\Throwable $e) {
                // ignore file deletion errors
            }
            
            $image->delete();
        }

        // Log activity
        ActivityLog::create([
            'action' => 'patient.delete',
            'user_id' => $request->user()->id,
            'patient_id' => $patient->id,
            'ip' => $request->ip(),
            'user_agent' => (string) $request->header('User-Agent'),
        ]);

        // Delete patient
        $patient->delete();

        return redirect()->route('patients.index')->with('status', 'Patient and all associated images deleted successfully');
    }
}


