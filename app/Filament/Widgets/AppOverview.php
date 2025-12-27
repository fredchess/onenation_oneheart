<?php

namespace App\Filament\Widgets;

use App\Enums\UserRoleEnum;
use App\Models\Donation;
use App\Models\Orphanage;
use App\Models\User;
use CyrildeWit\EloquentViewable\View;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppOverview extends BaseWidget
{
    protected function getStats(): array
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        if ($user->hasRole(UserRoleEnum::RESPONSABLE->value)) {
            return [
                Stat::make('Nb. de visites', $user->orphanages()->first() ? $user->orphanages->map(fn ($orphanage) => $orphanage->views()->count())->sum() : 0),
                Stat::make('Dons', function () use ($user) {
                    $ans = DB::table('donations')
                        ->join('orphanages', 'orphanages.id', '=', 'donations.orphanage_id')
                        ->where('orphanages.responsable_id', $user->id)
                        ->where('donations.status', true)
                        ->sum('donations.amount') . " FCFA";

                    return $ans;
                })->icon('heroicon-o-currency-dollar'),
            ];
        }

        return [
            Stat::make('Nb. de visites', View::count())
                ->icon('heroicon-o-eye'),
            Stat::make('Bénévoles', function () {
                return User::count();
            })->icon('heroicon-o-user'),
            Stat::make('Orphelinats', function () {
                return Orphanage::count();
            })->icon('heroicon-o-home'),
            Stat::make('Dons', function () {
                return Donation::query()
                        ->where('status', true)
                        ->sum('amount') . " FCFA";
            })->icon('heroicon-o-currency-dollar'),
        ];
    }
}
