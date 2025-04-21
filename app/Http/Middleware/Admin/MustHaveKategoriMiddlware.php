<?php

namespace App\Http\Middleware\Admin;

use App\Filament\Resources\KategoriResource;
use App\Models\Kategori;
use Closure;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MustHaveKategoriMiddlware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $totalKategori = Kategori::count();
        if ($totalKategori <= 0) {
            Notification::make()
                ->danger()
                ->title('Maaf!')
                ->body('Anda masih belum memiliki kategori. Silakan buat kategori terlebih dahulu.')
                ->actions([
                    Action::make('Buat Kategori')
                        ->url(KategoriResource::getUrl('create')),
                ])
                ->send();
            return back();
        }
        return $next($request);
    }
}
