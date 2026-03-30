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

                        /* Apply border radius to common elements */
                        .fi-section, .fi-btn, .fi-input, .fi-card, .fi-modal-window {
                            border-radius: {$radius} !important;
                        }
                    </style>
                ")
            );
        }

        return $next($request);
    }
}
