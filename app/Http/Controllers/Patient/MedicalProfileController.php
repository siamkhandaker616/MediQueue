<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\PatientMedicalProfile;
use Illuminate\Http\Request;

class MedicalProfileController extends Controller
{
    /**
     * FR-13: Allergy & Chronic Medical Profile View
     */
    public function edit()
    {
        $patientId = auth()->id() ?? 1;
        $profile = PatientMedicalProfile::firstOrCreate(['patient_id' => $patientId]);

        return view('patient.medical-profile', compact('profile'));
    }

    /**
     * FR-13: Update Medical Profile
     */
    public function update(Request $request)
    {
        $patientId = auth()->id() ?? 1;

        $validated = $request->validate([
            'blood_type'                     => 'nullable|string|max:5',
            'allergies'                      => 'nullable|array',
            'allergies.*'                    => 'string|max:50',
            'chronic_conditions'             => 'nullable|array',
            'chronic_conditions.*'           => 'string|max:50',
            'current_medications'            => 'nullable|array',
            'current_medications.*'          => 'string|max:100',
            'emergency_contact_name'         => 'nullable|string|max:100',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'emergency_contact_phone'        => 'nullable|string|max:20',
            'additional_notes'               => 'nullable|string|max:1000',
        ]);

        $emergencyContact = [
            'name'         => $request->emergency_contact_name,
            'relationship' => $request->emergency_contact_relationship,
            'phone'        => $request->emergency_contact_phone,
        ];

        PatientMedicalProfile::updateOrCreate(
            ['patient_id' => $patientId],
            [
                'blood_type'          => $validated['blood_type'] ?? null,
                'allergies'           => $validated['allergies'] ?? [],
                'chronic_conditions'  => $validated['chronic_conditions'] ?? [],
                'current_medications' => $validated['current_medications'] ?? [],
                'emergency_contact'   => $emergencyContact,
                'additional_notes'    => $validated['additional_notes'] ?? null,
                'last_updated'        => now(),
            ]
        );

        return back()->with('success', 'Medical profile and emergency alerts successfully updated!');
    }
}