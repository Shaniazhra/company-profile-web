<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email=admin@dreamratio.test} {--name=Admin} {--password=}';

    protected $description = 'Create or update the admin account';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->option('password') ?: Str::password(14, symbols: false);

        $user = User::firstOrNew(['email' => $email]);
        $user->forceFill([
            'name' => $this->option('name'),
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'is_admin' => true,
        ])->save();

        $this->info("Akun admin siap: {$email}");
        $this->line("Password: {$password}");
        $this->warn('Save this password now. It will not be shown again.');

        return self::SUCCESS;
    }
}