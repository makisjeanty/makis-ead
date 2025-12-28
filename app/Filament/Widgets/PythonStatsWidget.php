<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PythonStatsWidget extends BaseWidget
{
    // Define a ordem: aparece no topo da dashboard
    protected static ?int $sort = 1;
    
    // Atualiza a cada 15 segundos
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        // Se não tiver usuário logado, retorna vazio
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();

        // 1. Cria token temporário
        $tempToken = $user->createToken('InternalDashboardCall')->plainTextToken;

        try {
            // 2. Chama o Python (nome do serviço: python_api)
            $response = Http::withToken($tempToken)
                ->timeout(2)
                ->get('http://python_api:8000/dashboard/stats');
            
            // Limpa token
            $user->tokens()->where('name', 'InternalDashboardCall')->delete();

            if ($response->successful()) {
                $data = $response->json();
                $kpis = $data['kpis'] ?? [];

                return [
                    Stat::make('Total de Alunos', $kpis['total_students'] ?? 0)
                        ->description('Python Data Engine')
                        ->descriptionIcon('heroicon-m-users')
                        ->color('success'),

                    Stat::make('Engajamento IA', $kpis['engagement_score'] ?? '0%')
                        ->description('Algoritmo Python')
                        ->chart([7, 2, 10, 3, 15, 4, 17])
                        ->color('primary'),

                    Stat::make('Status do Python', 'Online')
                        ->description('Docker OK')
                        ->color('success'),
                ];
            }
        } catch (\Exception $e) {
            return [
                Stat::make('Conexão Python', 'Falha')
                    ->description('Verifique o container')
                    ->color('danger'),
            ];
        }

        return [
            Stat::make('Python API', 'Sem Resposta')->color('gray'),
        ];
    }
}
