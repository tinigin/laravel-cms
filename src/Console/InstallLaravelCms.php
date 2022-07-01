<?php

namespace LaravelCms\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelCms\LaravelCmsServiceProvider;
use LaravelCms\Models\Cms\Section;
use LaravelCms\Models\Cms\SectionGroup;
use LaravelCms\Models\Cms\User;

class InstallLaravelCms extends Command
{
    protected $signature = 'cms:install';

    protected $description = 'Install the Laravel CMS';

    public function handle()
    {
        $this->comment('Installation started. Please wait...');

        $this->info('Publishing configuration...');

        $this
            ->executeCommand('vendor:publish', [
                '--provider' => LaravelCmsServiceProvider::class,
                '--tag'      => [
                    'config',
                    'migrations',
                    'assets',
                    'lang',
                    'handler'
                ],
                '--force' => ''
            ])
            ->executeCommand('migrate')
            ->executeCommand('storage:link')
            ->initDbRecords()
            ->showMeLove();

        $this->info('Completed!');
    }

    protected function initDbRecords(): self
    {
        $group = SectionGroup::firstOrCreate([
            'name' => 'System',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $groups = Section::firstOrCreate([
            'name' => 'Groups',
            'folder' => 'section-groups',
            'cms_section_group_id' => $group->getKey(),
            'is_published' => true,
        ]);

        $sections = Section::firstOrCreate([
            'name' => 'Sections',
            'folder' => 'sections',
            'cms_section_group_id' => $group->getKey(),
            'is_published' => true,
        ]);

        $users = Section::firstOrCreate([
            'name' => 'Users',
            'folder' => 'users',
            'cms_section_group_id' => $group->getKey(),
            'is_published' => true,
        ]);

        $settings = Section::firstOrCreate([
            'name' => 'Settings',
            'folder' => 'settings',
            'cms_section_group_id' => $group->getKey(),
            'is_published' => true,
        ]);

        $user = User::firstOrCreate([
            'name' => $this->ask('What is your name?', 'admin'),
            'email' => $this->ask('What is your email?', 'admin@admin.com'),
            'password' => bcrypt($this->secret('What is the password?')),
            'status_id' => User::ACTIVE
        ]);

        $user->sections()->sync([
            $groups->getKey(),
            $sections->getKey(),
            $users->getKey(),
            $settings->getKey(),
        ]);

        $this->info('User created successfully.');

        return $this;
    }

    /**
     * @param string $command
     * @param array  $parameters
     *
     * @return $this
     */
    private function executeCommand(string $command, array $parameters = []): self
    {
        try {
            $result = $this->callSilent($command, $parameters);
        } catch (\Exception $exception) {
            $result = 1;
            $this->alert($exception->getMessage());
        }

        if ($result) {
            $parameters = http_build_query($parameters, '', ' ');
            $parameters = str_replace('%5C', '/', $parameters);
            $this->alert("An error has occurred. The '{$command} {$parameters}' command was not executed");
        }

        return $this;
    }

    /**
     * @param string $constant
     * @param string $value
     *
     * @return InstallLaravelCms
     */
    private function setValueEnv(string $constant, string $value = 'null'): self
    {
        $str = $this->fileGetContent(app_path('../.env'));

        if ($str !== false && strpos($str, $constant) === false) {
            file_put_contents(app_path('../.env'), $str . PHP_EOL . $constant . '=' . $value . PHP_EOL);
        }

        return $this;
    }

    /**
     * @param string $file
     *
     * @return false|string
     */
    private function fileGetContent(string $file)
    {
        if (! is_file($file)) {
            return '';
        }

        return file_get_contents($file);
    }

    /**
     * @return $this
     */
    private function showMeLove(): self
    {
        if (App::runningUnitTests() || ! $this->confirm('Would you like to show a little love by starting with ⭐')) {
            return $this;
        }

        $repo = 'https://github.com/tinigin/laravel-cms';

        switch (PHP_OS_FAMILY) {
            case 'Darwin':
                exec('open ' . $repo);
                break;
            case 'Windows':
                exec('start ' . $repo);
                break;
            case 'Linux':
                exec('xdg-open ' . $repo);
                break;
            default:
                $this->line("You can find us at " . $repo);
        }

        $this->line("Thank you! 🙏");

        return $this;
    }
}
