<?php

namespace Wikichua\Instant\Providers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, auth()->user()->password);
        }, 'The current password is invalid.');

        Validator::extend('at_least', function ($attribute, $value, $parameters, $validator) {
            return count(array_filter($value, function ($var) use ($parameters) {
                if (!is_array($parameters) || count($parameters) < 1) {
                    $parameters = [1];
                }
                return ($var && $var >= $parameters[0]);
            }));
        }, 'Please fill or select at least '.(isset($parameters[0])? $parameters[0]:1).' in :attribute:.');

        Validator::extend('all_filled', function ($attribute, $value, $parameters, $validator) {
            return count(array_filter($value, function ($var) use ($parameters) {
                if (is_null($var)) {
                    return false;
                }
                return true;
            }));
        }, 'Please fill up all the :attribute:.');
    }
}
