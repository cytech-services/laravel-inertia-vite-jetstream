<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Jetstream;

class DefaultUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default user';

    /**
     * The default user's name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The default user's email.
     *
     * @var string
     */
    protected $email = '';

    /**
     * The default user's password.
     *
     * @var string
     */
    protected $password = '';

    /**
     * The default user's confirm password.
     *
     * @var string
     */
    protected $confirm = '';

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
     * @global string $name
     * @global string $email
     * @global string $password
     * @return int
     */
    public function handle()
    {
        $this->_askName();
        $this->_askEmail();
        $this->_askPassword();
        $this->_askConfirm();

        $user = new User();
        $user->password = Hash::make($this->password);
        $user->email = $this->email;
        $user->name = $this->name;
        $user->save();

        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => 'Personal',
            'personal_team' => true,
        ]));

        $team = Team::where('name', 'Administrators')->first();
        if (!$team) {
            $team = Team::forceCreate([
                'user_id' => $user->id,
                'name' => "Administrators",
                'personal_team' => false,
            ]);
            $user->ownedTeams()->save($team);
        } else {
            $user->teams()->save($team);
        }

        return 0;
    }

    /**
     * Ask the user their full name
     * @global string $name
     * @return void
     */
    private function _askName(): void
    {
        $this->name = $this->ask('What is your full name?');
        if (trim($this->name) === '' || strlen(trim($this->name)) < 3) {
            $this->error('You must supply a full name');
            $this->_askName();
        }
    }

    /**
     * Ask the user their email address
     * @global string $email
     * @return void
     */
    private function _askEmail(): void
    {
        $this->email = $this->ask('What is your email? (used for login)');
        if (trim($this->email) === '' || strlen(trim($this->email)) < 3) {
            $this->error('You must supply an email');
            $this->_askEmail();
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->error('You must supply a valid email');
            $this->_askEmail();
        }
    }

    /**
     * Ask the user their password
     * @global string $password
     * @return void
     */
    private function _askPassword(): void
    {
        $this->password = $this->secret('What is the password?');
        if (trim($this->password) === '' || strlen(trim($this->password)) < 3) {
            $this->error('You must supply a password');
            $this->_askPassword();
        }
    }

    /**
     * Ask the user their password confirmation
     * @global string $confirm
     * @global string $password
     * @return void
     */
    private function _askConfirm(): void
    {
        $this->confirm = $this->secret('Confirm your password');
        if (trim($this->confirm) === '' || strlen(trim($this->confirm)) < 3) {
            $this->error('You must supply a password confirmation');
            $this->_askConfirm();
        } elseif ($this->password !== $this->confirm) {
            $this->error('Your password confirmation does not match your password');
            $this->_askConfirm();
        }
    }
}
