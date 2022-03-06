<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRandomPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-password {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a random password for a specified email.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Setup
        if (config('app.env', 'production') == 'production')
            if (!$this->confirm('This appears to be production. ARE YOU ABSOLUTELY SURE???'))
                return 0;

        // Start the work, we're clear for takeoff
        if ($this->argument('email')) {
            $email = $this->argument('email');
            if (!$this->confirm("Are you sure you want to generate a password for '$email'?"))
                return 0;
            $user = User::where('email', $email)->get()->first();
            $this->SetPassword($user);
        } else {
            $this->error('no email specified');
        }

        return 0;
    }

    public function SetPassword($user)
    {
        $randPassword = Str::random(12);
        $this->info("Setting password for $user->username to $randPassword");
        $user->password = Hash::make($randPassword);
        $user->save();
    }
}
