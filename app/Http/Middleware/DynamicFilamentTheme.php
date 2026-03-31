<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Symfony\Component\HttpFoundation\Response;

class DynamicFilamentTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Filament::auth()->user();

        if ($user && $user->settings) {
            // Register Colors
            FilamentColor::register([
                'primary' => match ($user->settings->primary_color) {
                    'blue' => Color::Blue,
                    'sky' => Color::Sky,
                    'cyan' => Color::Cyan,
                    'emerald' => Color::Emerald,
                    'teal' => Color::Teal,
                    'lime' => Color::Lime,
                    'amber' => Color::Amber,
                    'orange' => Color::Orange,
                    'rose' => Color::Rose,
                    'fuchsia' => Color::Fuchsia,
                    'violet' => Color::Violet,
                    'indigo' => Color::Indigo,
                    default => Color::Blue,
                },
            ]);

            // Register Top Navigation
            $panel = Filament::getCurrentOrDefaultPanel();
            if ($panel && method_exists($panel, 'topNavigation')) {
                $panel->topNavigation((bool) $user->settings->top_navigation);
            }

            // Register Font & UI Styles
            $font = $user->settings->font ?? 'Inter';
            $radius = match ($user->settings->border_radius ?? 'md') {
                'none' => '0px',
                'md' => '0.375rem',
                'lg' => '0.5rem',
                'xl' => '0.75rem',
                '2xl' => '1rem',
                default => '0.5rem',
            };
            $maxWidth = match ($user->settings->content_width ?? 'full') {
                'centered' => '80rem',
                default => 'none',
            };

            FilamentView::registerRenderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString("
                    <link rel='preconnect' href='https://fonts.googleapis.com'>
                    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
                    <link href='https://fonts.googleapis.com/css2?family={$font}:wght@400;500;600;700&display=swap' rel='stylesheet'>
                    <style>
                        :root {
                            --font-family: '{$font}', sans-serif;
                            --c-border-radius: {$radius};

                            /* Global Tailwind V4 Radius Overrides */
                            --radius-md: {$radius} !important;
                            --radius-lg: {$radius} !important;
                            --radius-xl: {$radius} !important;
                            --radius-2xl: {$radius} !important;
                        }
                        
                        body {
                            font-family: var(--font-family) !important;
                        }

                        /* Override Filament maximum width if centered */
                        .fi-main-ctn {
                            max-width: {$maxWidth} !important;
                            margin-left: auto !important;
                            margin-right: auto !important;
                        }

                        /* Apply border radius to common elements and custom containers */
                        .fi-section, .fi-btn, .fi-input, .fi-card, .fi-modal-window, 
                        .rounded-lg, .rounded-md, .rounded-xl, .rounded-2xl {
                            border-radius: var(--c-border-radius) !important;
                        }

                        /* Target specific buttons and areas in custom views */
                        .fi-main input[type='file']::file-selector-button {
                            border-radius: var(--c-border-radius) !important;
                        }

                        /* Ensure primary color for custom views (Tailwind Custom Colors mapping) */
                        @media screen {
                            :root {
                                --color-primary-50: rgb(var(--primary-50)) !important;
                                --color-primary-100: rgb(var(--primary-100)) !important;
                                --color-primary-200: rgb(var(--primary-200)) !important;
                                --color-primary-300: rgb(var(--primary-300)) !important;
                                --color-primary-400: rgb(var(--primary-400)) !important;
                                --color-primary-500: rgb(var(--primary-500)) !important;
                                --color-primary-600: rgb(var(--primary-600)) !important;
                                --color-primary-700: rgb(var(--primary-700)) !important;
                                --color-primary-800: rgb(var(--primary-800)) !important;
                                --color-primary-900: rgb(var(--primary-900)) !important;
                                --color-primary-950: rgb(var(--primary-950)) !important;
                            }
                        }
                    </style>
                ")
            );
        }

        return $next($request);
    }
}
