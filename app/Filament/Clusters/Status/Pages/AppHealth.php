<?php

namespace App\Filament\Clusters\Status\Pages;

use App\Filament\Clusters\Status;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class AppHealth extends Page
{
    use HasPageShield;

    protected static ?string $cluster = Status::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'App Health';
    protected static ?string $title = 'App Health';
    protected string $view = 'filament.clusters.status.pages.app-health';

    /**
     * Packages we want to track explicitly add or remove entries here as needed.
     *
     * @return array
     */
    private function trackedPackages(): array
    {
        return [
            [
                'name'  => 'laravel/framework',
                'label' => 'Laravel',
                'icon'  => 'heroicon-o-bolt',
            ],
            [
                'name'  => 'filament/filament',
                'label' => 'Filament',
                'icon'  => 'heroicon-o-sparkles',
            ],
        ];
    }

    /**
     * Parse composer.lock and return a map of package → installed version.
     *
     * @return array
     */
    private function getInstalledPackagesVersions(): array
    {
        $lockPath = base_path('composer.lock');

        if (!file_exists($lockPath)) {
            return [];
        }

        $lock     = json_decode(file_get_contents($lockPath), true);
        $packages = array_merge(
            $lock['packages']          ?? [],
            $lock['packages-dev']      ?? [],
        );

        $map = [];
        foreach ($packages as $pkg) {
            $map[$pkg['name']] = ltrim($pkg['version'] ?? '0.0.0', 'v');
        }

        return $map;
    }

    /**
     * Fetch the latest stable version from Packagist for a given package.
     * Returns null on failure so we can gracefully degrade.
     *
     * @param string $package
     * @return string|null
     */
    private function fetchLatestPackageVersion(string $package): ?string
    {
        $url  = "https://packagist.org/packages/{$package}.json";
        $ctx  = stream_context_create(['http' => ['timeout' => 5]]);

        $json = @file_get_contents($url, false, $ctx);

        if ($json === false) {
            return null;
        }

        $data     = json_decode($json, true);
        $versions = array_keys($data['package']['versions'] ?? []);

        // Keep only stable semver tags (v1.2.3 or 1.2.3), ignore dev/RC/etc.
        $stable = array_filter($versions, fn($v) => preg_match('/^v?\d+\.\d+\.\d+$/', $v));

        if (empty($stable)) {
            return null;
        }

        usort($stable, fn($a, $b) => version_compare(ltrim($b, 'v'), ltrim($a, 'v')));

        return ltrim($stable[0], 'v');
    }

    /**
     * Classify the difference between installed and latest versions.
     *
     * Returns one of: 'up_to_date' | 'patch' | 'minor' | 'major' | 'unknown'
     *
     * @param string $installed
     * @param string $latest
     * @return string
     */
    private function classifyPackageUpdate(string $installed, string $latest): string
    {
        if (version_compare($installed, $latest, '>=')) {
            return 'up_to_date';
        }

        [$iMaj, $iMin] = explode('.', $installed . '.0.0');
        [$lMaj, $lMin] = explode('.', $latest   . '.0.0');

        if ((int) $lMaj > (int) $iMaj) return 'major';
        if ((int) $lMin > (int) $iMin) return 'minor';

        return 'patch';
    }

    /**
     * Build the full package status array consumed by the view.
     *
     * @return array
     */
    public function getPackageStatuses(): array
    {
        $installed = $this->getInstalledPackagesVersions();

        return array_map(function (array $pkg) use ($installed) {
            $name      = $pkg['name'];
            $current   = $installed[$name] ?? null;
            $latest    = $current ? $this->fetchLatestPackageVersion($name) : null;
            $update    = ($current && $latest) ? $this->classifyPackageUpdate($current, $latest) : 'unknown';

            return [
                'name'      => $name,
                'label'     => $pkg['label'],
                'icon'      => $pkg['icon'],
                'current'   => $current  ?? 'n/a',
                'latest'    => $latest   ?? 'n/a',
                'update'    => $update,
            ];
        }, $this->trackedPackages());
    }

    /**
     * CPU usage percentage (1-second sample via /proc/stat).
     * Falls back to the `top` command if /proc/stat is unavailable.
     *
     * @return float
     */
    private function getCpuUsage(): float
    {
        if (!file_exists('/proc/stat')) {
            // macOS / BSD fallback
            $output = [];
            exec("top -bn1 | grep 'Cpu(s)' | awk '{print \$2}'", $output);
            return isset($output[0]) ? (float) $output[0] : 0.0;
        }

        // Read two snapshots 200 ms apart for an accurate percentage
        $read = function (): array {
            $line  = explode(' ', trim(file('/proc/stat')[0]));
            $times = array_slice(array_filter($line), 1); // drop "cpu" label
            return array_map('intval', array_values($times));
        };

        $a = $read();
        usleep(200_000);
        $b = $read();

        $deltaTotal = array_sum($b) - array_sum($a);
        $deltaIdle  = ($b[3] - $a[3]) + ($b[4] - $a[4]); // idle + iowait

        if ($deltaTotal === 0) {
            return 0.0;
        }

        return round((1 - $deltaIdle / $deltaTotal) * 100, 1);
    }

    /**
     * Memory stats in MB: ['total', 'used', 'free', 'percent']
     *
     * @return array
     */
    private function getMemoryUsage(): array
    {
        if (!file_exists('/proc/meminfo')) {
            return ['total' => 0, 'used' => 0, 'free' => 0, 'percent' => 0.0];
        }

        $lines = file('/proc/meminfo');
        $info  = [];

        foreach ($lines as $line) {
            [$key, $val] = array_pad(explode(':', $line, 2), 2, '0');
            $info[trim($key)] = (int) trim($val); // value is in kB
        }

        $total    = ($info['MemTotal']     ?? 0) / 1024;
        $free     = ($info['MemFree']      ?? 0) / 1024;
        $buffers  = ($info['Buffers']      ?? 0) / 1024;
        $cached   = ($info['Cached']       ?? 0) / 1024;
        $sreclmbl = ($info['SReclaimable'] ?? 0) / 1024;

        $available = $free + $buffers + $cached + $sreclmbl;
        $used      = $total - $available;
        $percent   = $total > 0 ? round(($used / $total) * 100, 1) : 0.0;

        return [
            'total'   => round($total,     1),
            'used'    => round($used,      1),
            'free'    => round($available, 1),
            'percent' => $percent,
        ];
    }

    /**
     * Disk usage for the application base path partition.
     * Returns ['total', 'used', 'free', 'percent'] in GB.
     *
     * @return array
     */
    private function getDiskUsage(): array
    {
        $path  = base_path();
        $total = disk_total_space($path);
        $free  = disk_free_space($path);
        $used  = $total - $free;

        $gb      = fn(int|float $bytes): float => round($bytes / 1_073_741_824, 2);
        $percent = $total > 0 ? round(($used / $total) * 100, 1) : 0.0;

        return [
            'total'   => $gb($total),
            'used'    => $gb($used),
            'free'    => $gb($free),
            'percent' => $percent,
        ];
    }

    /**
     * System load averages (1 / 5 / 15 minutes).
     *
     * @return array
     */
    private function getLoadAverage(): array
    {
        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];

        return [
            '1m'  => round($load[0] ?? 0, 2),
            '5m'  => round($load[1] ?? 0, 2),
            '15m' => round($load[2] ?? 0, 2),
        ];
    }

    /**
     * Bundle all server metrics for the view.
     *
     * @return array
     */
    public function getServerMetrics(): array
    {
        return [
            'cpu'    => $this->getCpuUsage(),
            'memory' => $this->getMemoryUsage(),
            'disk'   => $this->getDiskUsage(),
            'load'   => $this->getLoadAverage(),
        ];
    }
}
