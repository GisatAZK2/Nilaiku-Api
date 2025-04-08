<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\PredictionResult;
use Illuminate\Support\Facades\Cache;
use App\Models\Student;

class Stats extends BaseWidget
{
    protected function getStats(): array
    {
        $adminCount = Cache::remember('stats_admin_count', 60, function () {
            return User::where('role', 'admin')->count();
        });

        $siswaCount = Cache::remember('stats_siswa_count', 60, function () {
            return Student::count();
        });

        $predictionCount = Cache::remember('stats_prediction_count', 60, function () {
            return PredictionResult::count();
        });

        return [
            Stat::make('Total Admin', $adminCount)
                ->description('Akun dengan role admin')
                ->color('info')
                ->icon('heroicon-o-user-group'),

            Stat::make('Total Siswa', $siswaCount)
                ->description('Akun dengan role siswa')
                ->color('success')
                ->icon('heroicon-o-academic-cap'),

            Stat::make('Data Prediksi', $predictionCount)
                ->description('Total hasil prediksi tersimpan')
                ->color('warning')
                ->icon('heroicon-o-chart-bar'),
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
