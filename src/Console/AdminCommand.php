<?php

namespace LaravelCms\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use LaravelCms\Models\Cms\Section;
use LaravelCms\Models\Cms\User;

class AdminCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cms:admin';

    /**
     * @var string
     */
    protected $signature = 'cms:admin {name?} {email?} {password?} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user administrator';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $userId = $this->option('id');

            empty($userId)
                ? $this->createNewUser()
                : $this->updateUserPermissions((string) $userId);
        } catch (Exception | QueryException $e) {
            $this->error($e->getMessage());
        }
    }

    protected function createNewUser(): void
    {
        $this->createAdmin(
            $this->argument('name') ?? $this->ask('What is your name?', 'admin'),
            $this->argument('email') ?? $this->ask('What is your email?', 'admin@admin.com'),
            $this->argument('password') ?? $this->secret('What is the password?')
        );
    }

    public function createAdmin(string $name, string $email, string $password)
    {
        throw_if(User::where('email', $email)->exists(), 'User exist');

        $user = User::create([
            'name'        => $name,
            'email'       => $email,
            'password'    => bcrypt($password),
            'status_id'   => User::ACTIVE
        ]);

        $this->info('User created successfully.');

        $this->updateUserPermissions($user->getKey());
    }

    /**
     * @param string $id
     */
    protected function updateUserPermissions(string $id): void
    {
        $user = User::findOrFail($id);

        $user->sections()->sync(Section::all(['id']));

        $this->info('User sections restored');
    }
}
