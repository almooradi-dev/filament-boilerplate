<?php

namespace App\Filament\Clusters\Status\Pages;

use App\Filament\Clusters\Status;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class BackgroundServices extends Page
{
    use HasPageShield;
    
    protected static ?string $cluster = Status::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-command-line';
    protected static ?string $navigationLabel = 'Background Services';
    protected static ?string $title = 'Background Services';
    protected string $view = 'filament.clusters.status.pages.background-services';

    /**
     * Get services definitions
     *
     * @return array
     */
    public function getServices(): array
    {
        $basePath = base_path();

        return [
            [
                'key'         => 'queue_worker',
                'label'       => 'Queue Worker',
                'description' => 'Processes queued jobs in the background.',
                'icon'        => 'heroicon-o-queue-list',
                'check'       => "ps aux | grep '[p]hp artisan queue:work' | grep '{$basePath}'",
                'command'     => 'php artisan queue:work',
            ],
            // [
            //     'key'         => 'scheduler',
            //     'label'       => 'Task Scheduler',
            //     'description' => 'Runs scheduled artisan commands (every minute via cron).',
            //     'icon'        => 'heroicon-o-clock',
            //     'check'       => "ps aux | grep '[p]hp artisan schedule:work' | grep '{$basePath}'",
            //     'command'     => 'php artisan schedule:work',
            // ],
        ];
    }

    /**
     * Check if service is running
     *
     * @param string $checkCommand
     * @return boolean
     */
    private function isRunning(string $checkCommand): bool
    {
        exec($checkCommand, $output);
        return !empty($output);
    }

    /**
     * Get all services statuses
     *
     * @return array
     */
    public function getServiceStatuses(): array
    {
        return collect($this->getServices())
            ->map(fn($service) => array_merge($service, [
                'running' => $this->isRunning($service['check']),
            ]))
            ->toArray();
    }

    /**
     * Start a service
     *
     * @param string $key
     * @return void
     */
    public function startService(string $key): void
    {
        $service = collect($this->getServices())->firstWhere('key', $key);
        if (!$service) {
            return;
        }

        if ($this->isRunning($service['check'])) {
            Notification::make()->title("{$service['label']} is already running.")->warning()->send();
            return;
        }

        // We use proc_open() instead of exec() or shell_exec() because we need to
        // launch a truly detached background process (nohup ... &).
        //
        // exec() blocks PHP until the process exits — unusable for long-running daemons.
        // shell_exec() has the same problem and also doesn't return an exit code.
        //
        // proc_open() lets us control all three standard streams (stdin, stdout, stderr).
        // By routing stdout and stderr to /dev/null, the child process has no open pipe
        // back to PHP, so it detaches immediately and PHP does not wait for it.
        $errorLog = tempnam(sys_get_temp_dir(), 'artisan_start_');
        $basePath = base_path();

        // bash -c backgrounds the process inside the shell (&), so PHP never waits for it.
        // stderr is captured to $errorLog so we can report failures.
        // We no longer use proc_open descriptors for stderr since the shell handles redirection.
        $command = "bash -c 'cd {$basePath} && {$service['command']} > /dev/null 2>{$errorLog} &'";

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'], // stdout — just a pipe, won't block since process is backgrounded
            2 => ['pipe', 'w'], // stderr — same
        ];

        $process = proc_open($command, $descriptors, $pipes, $basePath);

        if (is_resource($process)) {
            proc_close($process);
        }

        sleep(1);
        $started = $this->isRunning($service['check']);

        if ($started) {
            Notification::make()
                ->title("{$service['label']} started successfully.")
                ->success()
                ->send();
        } else {
            // Read whatever the process wrote to stderr to surface the real failure reason.
            $error = file_exists($errorLog) ? trim(file_get_contents($errorLog)) : null;

            Notification::make()
                ->title("Failed to start {$service['label']}.")
                ->body($error ?: 'No error output captured. The process may lack permission or the command path is wrong.')
                ->danger()
                ->persistent() // Keep it visible so you have time to read the reason
                ->send();
        }

        // Clean up the temp file regardless of outcome.
        if (file_exists($errorLog)) {
            unlink($errorLog);
        }
    }

    private function buildStopCommand(string $command): string
    {
        $basePath = base_path();
        $script   = tempnam(sys_get_temp_dir(), 'artisan_stop_script_');

        file_put_contents($script, <<<BASH
            #!/bin/bash

            # Step 1: Find the bash -c wrapper PID (scoped to this app's base path)
            BASH_PID=\$(ps aux | grep '[b]ash -c cd {$basePath}' | grep '{$command}' | awk '{print \$2}')

            if [ -z "\$BASH_PID" ]; then
                echo "No bash wrapper found for {$command}"
                exit 1
            fi

            # Step 2: Find the PHP child process by looking for processes whose parent is the bash wrapper
            PHP_PID=\$(ps --ppid \$BASH_PID -o pid= 2>/dev/null)

            # Step 3: Kill the PHP child first, then the bash wrapper
            if [ ! -z "\$PHP_PID" ]; then
                kill -9 \$PHP_PID
            fi

            kill -9 \$BASH_PID

            BASH
        );

        chmod($script, 0755);

        return $script;
    }

    /**
     * Stop a service
     *
     * @param string $key
     * @return void
     */
    public function stopService(string $key): void
    {
        $service = collect($this->getServices())->firstWhere('key', $key);
        if (!$service) return;

        $errorLog  = tempnam(sys_get_temp_dir(), 'artisan_stop_');
        $script    = $this->buildStopCommand($service['command']);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['file', $errorLog, 'w'],
        ];

        // Execute the script file directly — no bash -c wrapper, no quote issues
        $process = proc_open("bash {$script}", $descriptors, $pipes, base_path());

        if (is_resource($process)) {
            proc_close($process);
        }

        sleep(1);
        $stillRunning = $this->isRunning($service['check']);

        if (!$stillRunning) {
            Notification::make()
                ->title("{$service['label']} stopped successfully.")
                ->success()
                ->send();
        } else {
            $error = file_exists($errorLog) ? trim(file_get_contents($errorLog)) : null;

            Notification::make()
                ->title("Failed to stop {$service['label']}.")
                ->body($error ?: 'No error output captured.')
                ->danger()
                ->persistent()
                ->send();
        }

        // Clean up both temp files
        if (file_exists($errorLog)) unlink($errorLog);
        if (file_exists($script))   unlink($script);
    }

    /**
     * Restart a service
     *
     * @param string $key
     * @return void
     */
    public function restartService(string $key): void
    {
        $this->stopService($key);
        sleep(1);
        $this->startService($key);
    }
}
