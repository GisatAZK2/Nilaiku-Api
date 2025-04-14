<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SystemInformation
{
    public function getTotalRamInGb(): ?string
    {
        return Cache::remember('system_total_ram', now()->addMinutes(10), function () {
            $os = strtoupper(PHP_OS);

            if (str_starts_with($os, 'WIN')) {
                return $this->getWindowsRam();
            }

            if (file_exists('/proc/meminfo')) {
                return $this->getLinuxRam();
            }

            return null;
        });
    }

    private function getLinuxRam(): ?string
    {
        $contents = file_get_contents('/proc/meminfo');

        if (preg_match('/^MemTotal:\s+(\d+)\skB$/m', $contents, $matches)) {
            $totalKb = (int) $matches[1];
            $totalGb = round($totalKb / 1024 / 1024, 2);
            return "$totalGb GB";
        }

        return null;
    }

    private function getWindowsRam(): ?string
    {
        try {
            $cmd    = 'powershell -Command "Get-WmiObject Win32_PhysicalMemory | Measure-Object -Property Capacity -Sum | ForEach-Object { [Math]::Round($_.Sum / 1GB, 2) }"';
            $output = shell_exec($cmd);

            if ($output !== null) {
                $totalGb = trim($output);
                return "$totalGb GB";
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
