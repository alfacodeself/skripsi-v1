<?php

namespace App\Http\Middleware\Admin;

use App\Filament\Resources\JenisBiayaResource;
use App\Models\JenisBiaya;
use Closure;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustHaveJenisBiayaMiddlware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $totalJenisBiaya = JenisBiaya::count();
        if ($totalJenisBiaya <= 0) {
            Notification::make()
                ->danger()
                ->title('Maaf!')
                ->body('Anda masih belum memiliki jenis biaya. Silakan buat jenis biaya terlebih dahulu.')
                ->actions([
                    Action::make('Buat Jenis Biaya')
                        ->url(JenisBiayaResource::getUrl('create')),
                ])
                ->send();
            return back();
        }
        return $next($request);
    }
}
