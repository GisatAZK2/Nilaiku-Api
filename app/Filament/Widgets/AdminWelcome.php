<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\Systeminformation;

class AdminWelcome extends Widget
{
    protected static string $view = 'filament.widgets.admin-welcome';


    public function getViewData(): array
    {
        $service = app(Systeminformation::class);

        return [
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'ram' => $service->getTotalRamInGb() ?? 'Tidak diketahui',
            'disk' => round(disk_total_space("/") / 1024 / 1024 / 1024, 2) . ' GB',
        ];
    }

    public function getColumnSpan(): int | string | array
    {
        return [
            'lg' => 5,
            'sm' => 2,
        ]; // Atau bisa juga angka seperti 2, 3, dst
    }
}
