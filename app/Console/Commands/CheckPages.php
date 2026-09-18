<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Throwable;

class CheckPages extends Command
{
    protected $signature = 'check:pages
                            {--user= : User ID yang digunakan untuk authentication}';

    protected $description = 'Check all GET routes as an authenticated user';

    public function handle(): int
    {
        $this->info('Laravel Page Checker');
        $this->line('Checking pages as authenticated user...');
        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | Find user
        |--------------------------------------------------------------------------
        */

        $userId = $this->option('user');

        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::first();
        }

        if (!$user) {
            $this->error('No user found in the users table.');

            $this->line(
                'Create a user first or use: php artisan check:pages --user=ID'
            );

            return self::FAILURE;
        }

        $this->line(
            "Authenticated as: {$user->email}"
        );

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | Get routes
        |--------------------------------------------------------------------------
        */

        $routes = collect(Route::getRoutes())
            ->filter(function ($route) {

                // Only GET routes
                if (!in_array('GET', $route->methods())) {
                    return false;
                }

                // Skip routes requiring parameters
                if (preg_match('/\{[^}]+\}/', $route->uri())) {
                    return false;
                }

                // Skip internal Laravel routes
                if (
                    str_starts_with($route->uri(), 'storage/')
                    || $route->uri() === 'up'
                ) {
                    return false;
                }

                return true;
            })
            ->unique(fn($route) => $route->uri())
            ->sortBy(fn($route) => $route->uri());

        /*
        |--------------------------------------------------------------------------
        | Counters
        |--------------------------------------------------------------------------
        */

        $success = 0;
        $redirected = 0;
        $forbidden = 0;
        $notFound = 0;
        $serverError = 0;
        $otherErrors = 0;

        /*
        |--------------------------------------------------------------------------
        | Check routes
        |--------------------------------------------------------------------------
        */

        foreach ($routes as $route) {

            $uri = $route->uri();

            $url = url($uri);

            try {

                /*
                |--------------------------------------------------------------------------
                | Create request
                |--------------------------------------------------------------------------
                */

                $request = Request::create(
                    $url,
                    'GET'
                );

                /*
                |--------------------------------------------------------------------------
                | Authenticate user
                |--------------------------------------------------------------------------
                */

                Auth::login($user);

                /*
                |--------------------------------------------------------------------------
                | Send request through Laravel
                |--------------------------------------------------------------------------
                */

                $response = app()->handle($request);

                $status = $response->getStatusCode();

                /*
                |--------------------------------------------------------------------------
                | Handle response
                |--------------------------------------------------------------------------
                */

                if ($status >= 200 && $status < 300) {

                    $this->line(
                        "<fg=green>✓</> {$uri} <fg=green>{$status}</>"
                    );

                    $success++;
                } elseif ($status >= 300 && $status < 400) {

                    $location = $response->headers->get('Location');

                    $this->line(
                        "<fg=yellow>↪</> {$uri} <fg=yellow>{$status}</>"
                    );

                    if ($location) {
                        $this->line(
                            "    → {$location}"
                        );
                    }

                    $redirected++;
                } elseif ($status === 403) {

                    $this->line(
                        "<fg=red>✗</> {$uri} <fg=red>403 FORBIDDEN</>"
                    );

                    $forbidden++;
                } elseif ($status === 404) {

                    $this->line(
                        "<fg=red>✗</> {$uri} <fg=red>404 NOT FOUND</>"
                    );

                    $notFound++;
                } elseif ($status >= 500) {

                    $this->line(
                        "<fg=red>✗</> {$uri} <fg=red>{$status} SERVER ERROR</>"
                    );

                    $this->showErrorDetails($response);

                    $serverError++;
                } else {

                    $this->line(
                        "<fg=red>✗</> {$uri} <fg=red>{$status}</>"
                    );

                    $otherErrors++;
                }
            } catch (Throwable $e) {

                $this->line(
                    "<fg=red>✗</> {$uri} <fg=red>EXCEPTION</>"
                );

                $this->line(
                    "    {$e->getMessage()}"
                );

                $serverError++;
            }

            /*
            |--------------------------------------------------------------------------
            | Logout after each request
            |--------------------------------------------------------------------------
            */

            Auth::logout();
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $this->newLine();

        $this->info('Summary');

        $this->line(
            "<fg=green>✓ Success       : {$success}</>"
        );

        $this->line(
            "<fg=yellow>↪ Redirect      : {$redirected}</>"
        );

        $this->line(
            "<fg=red>✗ Forbidden     : {$forbidden}</>"
        );

        $this->line(
            "<fg=red>✗ Not Found     : {$notFound}</>"
        );

        $this->line(
            "<fg=red>✗ Server Error  : {$serverError}</>"
        );

        $this->line(
            "<fg=red>✗ Other Error   : {$otherErrors}</>"
        );

        $this->newLine();

        if (
            $serverError > 0 ||
            $notFound > 0 ||
            $forbidden > 0 ||
            $otherErrors > 0
        ) {
            $this->error(
                'Some pages have errors. Check the output above.'
            );

            return self::FAILURE;
        }

        $this->info(
            'All tested pages passed.'
        );

        return self::SUCCESS;
    }

    /**
     * Show useful information from an error response.
     */
    private function showErrorDetails($response): void
    {
        $content = $response->getContent();

        /*
        |--------------------------------------------------------------------------
        | Try to get <title>
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/<title>(.*?)<\/title>/is',
                $content,
                $matches
            )
        ) {
            $title = trim(strip_tags($matches[1]));

            if ($title !== '') {
                $this->line(
                    "    {$title}"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Try to extract Laravel exception message
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/<h1[^>]*>(.*?)<\/h1>/is',
                $content,
                $matches
            )
        ) {
            $message = trim(strip_tags($matches[1]));

            if ($message !== '') {
                $this->line(
                    "    {$message}"
                );
            }
        }
    }
}
