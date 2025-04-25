<?php

namespace App\Filament\Landingpage\Pages;

use App\Models\Landingpage;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class HomePage extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.landingpage.pages.home-page';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Beranda';
    protected ?string $heading = '';

    protected int|string|array $columnSpan = 'full';

    public $landingpage;

    public function mount()
    {
        // dd(HomePage::getRouteName());
        $this->landingpage = Landingpage::query()->orderBy('order')->get()->map(function ($page) {
            $content = html_entity_decode(str_replace('<pre>', '', str_replace('</pre>', '', $page->content)));

            // Merender Blade yang ada di dalam konten
            $page->content = new HtmlString(Blade::render($content, ['landingpage' => $page]));
            return $page;
        });
        // dd($this->landingpage);
    }
}
