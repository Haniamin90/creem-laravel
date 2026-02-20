<?php

namespace Creem\Laravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class WebhookSecretCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creem:webhook-secret
                            {--show : Display the current webhook secret (masked)}
                            {--plain : Show the full unmasked secret (use with --show)}
                            {--force : Overwrite an existing webhook secret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate or display the CREEM webhook secret';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('show')) {
            return $this->showSecret();
        }

        return $this->generateSecret();
    }

    /**
     * Display the current webhook secret.
     */
    protected function showSecret(): int
    {
        $secret = config('creem.webhook_secret');

        if (empty($secret)) {
            $this->error('No CREEM webhook secret is configured.');
            $this->line('Set CREEM_WEBHOOK_SECRET in your .env file or run this command without --show to generate one.');

            return self::FAILURE;
        }

        $this->info('Current CREEM webhook secret:');

        if ($this->option('plain')) {
            $this->line($secret);
        } else {
            $masked = substr($secret, 0, 6).'...'.substr($secret, -4);
            $this->line($masked);
            $this->line('(Use --show --plain to display the full secret)');
        }

        return self::SUCCESS;
    }

    /**
     * Generate a new webhook secret and add it to .env.
     */
    protected function generateSecret(): int
    {
        $currentSecret = config('creem.webhook_secret');

        if (! empty($currentSecret) && ! $this->option('force')) {
            $this->warn('A CREEM webhook secret already exists.');
            $this->line('Use --force to overwrite it, or --show to display the current value.');

            return self::FAILURE;
        }

        $secret = 'whsec_'.Str::random(32);

        if (! $this->setEnvValue('CREEM_WEBHOOK_SECRET', $secret)) {
            $this->error('Failed to write to .env file.');
            $this->line("You can manually set: CREEM_WEBHOOK_SECRET={$secret}");

            return self::FAILURE;
        }

        $this->info('CREEM webhook secret generated successfully.');
        $this->line("Secret: {$secret}");
        $this->newLine();
        $this->line('Add this secret to your CREEM dashboard under Developers > Webhook.');

        return self::SUCCESS;
    }

    /**
     * Set a value in the .env file.
     */
    protected function setEnvValue(string $key, string $value): bool
    {
        $envPath = $this->laravel->basePath('.env');

        if (! file_exists($envPath)) {
            return false;
        }

        $content = file_get_contents($envPath);

        if (str_contains($content, "{$key}=")) {
            $content = preg_replace(
                '/^'.preg_quote($key, '/').'=.*/m',
                "{$key}={$value}",
                $content
            );
        } else {
            $content .= "\n{$key}={$value}\n";
        }

        return file_put_contents($envPath, $content) !== false;
    }
}
