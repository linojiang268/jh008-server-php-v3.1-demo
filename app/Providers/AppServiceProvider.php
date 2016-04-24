<?php
namespace App\Providers;

use Auth, Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         $this->extendValidator();
//        $this->extendAuthManager();
    }
    
//    private function extendAuthManager()
//    {
//        Auth::extend('extended-eloquent', function ($app) {
//            // AuthManager allows us only provide UserProvider instead of
//            // the whole Guard implmenetation
//            return new \Jihe\Auth\UserProvider(new \Jihe\Hashing\PasswordHasher(),
//                                               $app['config']['auth.model']);
//        });
//    }
    
     private function extendValidator()
     {
         Validator::extend('mobile', function($attribute, $value, $parameters) {
             return preg_match('/^1\d{10}$/', $value) > 0;
         });
     }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 
    }
    
}
