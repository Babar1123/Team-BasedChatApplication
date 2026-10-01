<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($message = null, $data = null, $meta = null) {
            $payload = [
                'success' => true,
                'message' => $message ?? 'OK',
                'data' => $data,
            ];

            if ($meta !== null) {
                $payload['meta'] = $meta;
            }

            return response()->json($payload);
        });

        Response::macro('error', function ($message = null, $status = 400, $data = null) {
            $payload = [
                'success' => false,
                'message' => $message ?? 'Error',
                'data' => $data,
            ];

            return response()->json($payload, $status);
        });
    }
}
