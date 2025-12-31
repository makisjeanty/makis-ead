<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Course;
use App\Models\User;
use App\Models\Order;
use App\Models\Enrollment;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Cursos', Course::count())
                ->description('Cursos publicados: ' . Course::where('is_published', true)->count())
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]), // Dados fictícios para "vida"
                
            Stat::make('Total de Alunos', User::where('role', 'student')->count())
                ->description('Novos este mês: ' . User::where('role', 'student')->whereMonth('created_at', now()->month)->count())
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([2, 5, 8, 4, 9, 12, 15]),
                
            Stat::make('Receita Total', 'R$ ' . number_format(Order::where('status', 'paid')->sum('total'), 2, ',', '.'))
                ->description('Este mês: R$ ' . number_format(Order::where('status', 'paid')->whereMonth('created_at', now()->month)->sum('total'), 2, ',', '.'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([150, 200, 180, 220, 300, 250, 400]),
                
            Stat::make('Matrículas Ativas', Enrollment::count())
                ->description('Concluídas: ' . Enrollment::whereNotNull('completed_at')->count())
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('warning')
                ->chart([4, 6, 3, 8, 10, 12, 14]),
        ];
    }
}
