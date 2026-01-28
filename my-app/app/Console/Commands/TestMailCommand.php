<?php

namespace App\Console\Commands;

use App\Mail\ParticipantVerificationEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test verification email to check SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Sending test email to: {$email}");
        $this->line("Mail Driver: " . config('mail.default'));
        $this->line("SMTP Host: " . config('mail.mailers.smtp.host'));
        $this->line("SMTP Port: " . config('mail.mailers.smtp.port'));
        $this->line("From: " . config('mail.from.address'));

        try {
            Mail::to($email)->send(new ParticipantVerificationEmail('123456', 'Test User'));
            $this->info("✓ Email sent successfully!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("✗ Email failed to send!");
            $this->error("Error: " . $e->getMessage());
            $this->line("");
            $this->line("Troubleshooting:");
            $this->line("1. Check that MAIL_MAILER=smtp in .env");
            $this->line("2. For Gmail: Enable 2FA and use an App Password (16-char) in MAIL_PASSWORD");
            $this->line("3. Verify MAIL_HOST and MAIL_PORT are correct");
            $this->line("4. Check logs: tail -f storage/logs/laravel.log");
            return Command::FAILURE;
        }
    }
}
