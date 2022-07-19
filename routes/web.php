<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/lang',[
    'uses' => 'App\Http\Controllers\HomeController@lang',
    'as' => 'lang.index'
]);

Route::get('/', function () {
    try {
        DB::connection()->getPdo();
        if (!Schema::hasTable('application_settings'))
            return redirect('dashboard');
    } catch (\Exception $e) {
        return redirect('dashboard');
    }
    return redirect('dashboard');
});



Auth::routes(['register' => false]);

Route::get('/users/create',[
    'uses' => 'App\Http\Controllers\UserController@create',
    'as' => 'users.create'
]);

Route::post('/users/store',[
    'uses' => 'App\Http\Controllers\UserController@store',
    'as' => 'users.store'
]);


Route::group(['middleware' => ['auth']], function() {

    Route::resources([
        'customer' => App\Http\Controllers\CustomerController::class,
        'item' => App\Http\Controllers\ItemController::class,

    ]);


    Route::get('/profile/setting',[
        'uses' => 'App\Http\Controllers\ProfileController@setting',
        'as' => 'profile.setting'
    ]);

    Route::post('/profile/updateSetting',[
        'uses' => 'App\Http\Controllers\ProfileController@updateSetting',
        'as' => 'profile.updateSetting'
    ]);
    Route::get('/profile/password',[
        'uses' => 'App\Http\Controllers\ProfileController@password',
        'as' => 'profile.password'
    ]);

    Route::post('/profile/updatePassword',[
        'uses' => 'App\Http\Controllers\ProfileController@updatePassword',
        'as' => 'profile.updatePassword'
    ]);
    Route::get('/profile/view',[
        'uses' => 'App\Http\Controllers\ProfileController@view',
        'as' => 'profile.view'
    ]);

});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboard',[
        'uses' => 'App\Http\Controllers\DashboardController@index',
        'as' => 'dashboard'
    ]);
});

Route::get('/home', function() {
    return redirect()->to('dashboard');
});
