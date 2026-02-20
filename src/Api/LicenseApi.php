<?php

namespace Creem\Laravel\Api;

use Creem\Laravel\CreemClient;

class LicenseApi
{
    protected CreemClient $client;

    public function __construct(CreemClient $client)
    {
        $this->client = $client;
    }

    /**
     * Activate a license key for a device/instance.
     *
     * @param  string  $key  The license key.
     * @param  string  $instanceName  The device/instance name.
     * @return array<string, mixed>
     */
    public function activate(string $key, string $instanceName): array
    {
        return $this->client->post('v1/licenses/activate', [
            'key' => $key,
            'instance_name' => $instanceName,
        ]);
    }

    /**
     * Validate a license key for a specific instance.
     *
     * @param  string  $key  The license key.
     * @param  string  $instanceId  The instance ID from activation.
     * @return array<string, mixed>
     */
    public function validate(string $key, string $instanceId): array
    {
        return $this->client->post('v1/licenses/validate', [
            'key' => $key,
            'instance_id' => $instanceId,
        ]);
    }

    /**
     * Deactivate a license key for a device/instance.
     *
     * @param  string  $key  The license key.
     * @param  string  $instanceId  The instance ID from activation.
     * @return array<string, mixed>
     */
    public function deactivate(string $key, string $instanceId): array
    {
        return $this->client->post('v1/licenses/deactivate', [
            'key' => $key,
            'instance_id' => $instanceId,
        ]);
    }
}
