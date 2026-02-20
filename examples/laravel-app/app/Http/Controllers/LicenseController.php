<?php

namespace App\Http\Controllers;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Facades\Creem;
use Illuminate\Http\Request;

/**
 * License key management.
 *
 * CREEM supports software licensing with activation limits.
 * After a checkout with a license-enabled product, the customer
 * receives a license key. Your app then activates, validates,
 * and deactivates instances of that license.
 */
class LicenseController extends Controller
{
    /**
     * Activate a license key for a new device/instance.
     *
     * Call this when a user enters their license key in your app.
     * Each activation creates a unique instance_id that you store
     * locally to validate and deactivate later.
     *
     * Demonstrates: Creem::activateLicense()
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'instance_name' => 'required|string|max:255',
        ]);

        try {
            $result = Creem::activateLicense(
                $request->license_key,
                $request->instance_name
            );

            // Store the instance_id locally — you need it for validation
            // and deactivation. Example: save to a `license_instances` table.

            return back()->with('success', 'License activated successfully.')
                ->with('license_data', $result);
        } catch (CreemApiException $e) {
            return back()->with('error', "Activation failed: {$e->getMessage()}");
        }
    }

    /**
     * Validate an active license instance.
     *
     * Call this on app startup or periodically to verify the license
     * is still valid. Returns the license status and metadata.
     *
     * Demonstrates: Creem::validateLicense()
     */
    public function validate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'instance_id' => 'required|string',
        ]);

        try {
            $result = Creem::validateLicense(
                $request->license_key,
                $request->instance_id
            );

            return response()->json([
                'valid' => true,
                'license' => $result,
            ]);
        } catch (CreemApiException $e) {
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Deactivate a license instance (e.g., user logs out of a device).
     *
     * Frees up an activation slot so the license can be used elsewhere.
     *
     * Demonstrates: Creem::deactivateLicense()
     */
    public function deactivate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'instance_id' => 'required|string',
        ]);

        try {
            Creem::deactivateLicense(
                $request->license_key,
                $request->instance_id
            );

            return back()->with('success', 'License deactivated. Activation slot freed.');
        } catch (CreemApiException $e) {
            return back()->with('error', "Deactivation failed: {$e->getMessage()}");
        }
    }
}
