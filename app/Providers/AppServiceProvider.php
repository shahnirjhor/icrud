<?php

namespace App\Providers;

use View;
use App\Models\ApplicationSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (request()->is('install'))
            return;

        Paginator::useBootstrap();
        view()->composer('*', function ($view) {
            $getLang = array (
                'en' => 'English',
                'bn' => 'বাংলা',
                'el' => 'Ελληνικά',
                'pt' => 'Português',
                'es' => 'Español',
                'de' => 'Deutsch',
                'fr' => 'Français',
                'nl' => 'Nederlands',
                'it' => 'Italiano',
                'vi' => 'Tiếng Việt',
                'ru' => 'русский',
                'tr' => 'Türkçe'
            );

            $flag = array(
                "en"=>"flag-icon-us",
                "bn"=>"flag-icon-bd",
                "el"=>"flag-icon-gr",
                "pt"=>"flag-icon-pt",
                "es"=>"flag-icon-es",
                "de"=>"flag-icon-de",
                "fr"=>"flag-icon-fr",
                "nl"=>"flag-icon-nl",
                "it"=>"flag-icon-it",
                "vi"=>"flag-icon-vn",
                "ru"=>"flag-icon-ru",
                "tr"=>"flag-icon-tr"
            );
            $user = [];
            if (Auth::check()) {
                $user = Auth::user();
            }
            $view->with('getLang', $getLang)->with('user', $user)->with('flag', $flag);
        });

    }
}
