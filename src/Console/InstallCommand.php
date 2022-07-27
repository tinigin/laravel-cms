<?php

namespace LaravelCms\Console;

use Illuminate\Console\Command;
use LaravelCms\LaravelCmsServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'cms:install';

    protected $description = 'Install Laravel CMS';

    public function handle()
    {
        $this->comment('Installation started. Please wait...');

        $this->info('Publishing config, assets and langs data..');
        $this->executeCommand('vendor:publish', [
                '--provider' => LaravelCmsServiceProvider::class,
                '--tag'      => [
                    'config',
                    'assets',
                    'lang'
                ]
            ]);

        $this->info('Running migrations..');
        $this->executeCommand('migrate');

        $this->info('Creating storage link..');
        $this->executeCommand('storage:link');

        $this->info('Updating error handler and encrypted cookies..');
        $this->updateErrorHandler()->updateEncryptedCookies();

        $this->info('Creating sections records in DB..');
        $this->executeCommand('cms:init-sections');

        $this->line('');
        $this->line('Run `php artisan cms:admin` to setup admin user');
        $this->line('');

        $this->info('Installation completed!');
    }

    protected function updateEncryptedCookies(): self
    {
        $str = $this->fileGetContent(app_path('Http/Middleware/EncryptCookies.php'));

        if ($str !== false && strpos($str, 'active_tab') === false) {
            file_put_contents(
                app_path('Http/Middleware/EncryptCookies.php'),
                str_replace(
                    "protected \$except = [",
                    'protected $except = [' . PHP_EOL . '        \'active_tab\'',
                    $str
                )
            );
        }

        return $this;
    }

    protected function updateErrorHandler(): self
    {
        $str = $this->fileGetContent(app_path('Exceptions/Handler.php'));

        if ($str !== false && strpos($str, 'LaravelCms\Exceptions\Handler') === false) {
            file_put_contents(
                app_path('Exceptions/Handler.php'),
                str_replace(
                    "use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;",
                    'use LaravelCms\Exceptions\Handler as ExceptionHandler;',
                    $str
                )
            );
        }

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
}
