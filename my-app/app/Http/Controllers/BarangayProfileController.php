<?php

namespace App\Http\Controllers;

use App\Models\BarangayProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarangayProfileController extends Controller
{
    /**
     * Display the barangay profile (or create form if none exists)
     */
    public function index()
    {
        // Only LGU Admin can access
        $this->authorizeAdmin();

        $profile = BarangayProfile::first();

        return view('app', [
            'section' => 'barangay_profile',
            'profile' => $profile,
        ]);
    }

    /**
     * Store a newly created barangay profile
     */
    public function store(Request $request)
    {
        // Only LGU Admin can create
        $this->authorizeAdmin();

        // Ensure only one profile exists
        if (BarangayProfile::exists()) {
            return redirect()->route('barangay.profile')
                ->with('error', 'Barangay profile already exists. Please edit the existing profile.');
        }

        $data = $request->validate([
            'barangay_name' => ['required', 'string', 'max:255'],
            'municipality_city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'barangay_address' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'estimated_population' => ['nullable', 'integer', 'min:0'],
            'area_classification' => ['nullable', 'string', 'in:Urban,Rural,Coastal,Mountainous'],
            'hazards' => ['nullable', 'array'],
            'hazards.*' => ['string'],
            'hazard_notes' => ['nullable', 'string'],
        ]);

        // Filter out empty hazards
        if (isset($data['hazards'])) {
            $data['hazards'] = array_values(array_filter($data['hazards']));
            if (empty($data['hazards'])) {
                $data['hazards'] = null;
            }
        }

        BarangayProfile::create($data);

        return redirect()->route('barangay.profile')
            ->with('status', 'Barangay profile created successfully.');
    }

    /**
     * Update the barangay profile
     */
    public function update(Request $request, BarangayProfile $barangayProfile)
    {
        // Only LGU Admin can update
        $this->authorizeAdmin();

        $data = $request->validate([
            'barangay_name' => ['required', 'string', 'max:255'],
            'municipality_city' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'barangay_address' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'estimated_population' => ['nullable', 'integer', 'min:0'],
            'area_classification' => ['nullable', 'string', 'in:Urban,Rural,Coastal,Mountainous'],
            'hazards' => ['nullable', 'array'],
            'hazards.*' => ['string'],
            'hazard_notes' => ['nullable', 'string'],
        ]);

        // Filter out empty hazards
        if (isset($data['hazards'])) {
            $data['hazards'] = array_values(array_filter($data['hazards']));
            if (empty($data['hazards'])) {
                $data['hazards'] = null;
            }
        }

        $barangayProfile->update($data);

        return redirect()->route('barangay.profile')
            ->with('status', 'Barangay profile updated successfully.');
    }

    /**
     * Ensure only LGU Admin can access
     */
    protected function authorizeAdmin(): void
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'LGU_ADMIN') {
            abort(403, 'Unauthorized. Only LGU Admin can access this module.');
        }
    }
}
