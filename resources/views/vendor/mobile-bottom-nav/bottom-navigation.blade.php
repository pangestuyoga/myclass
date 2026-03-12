@php
    use function Filament\Support\generate_href_html;
    use function Filament\Support\generate_icon_html;
    use Filament\Support\Enums\IconSize;
@endphp
<style data-navigate-track>
    .fi-bottom-nav {
        position: fixed;
        bottom: 1.5rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 50;

        display: flex;
        align-items: center;
        justify-content: space-around;

        width: calc(100% - 2.5rem);
        max-width: 440px;
        height: 4.5rem;

        padding: 0 0.75rem;
        padding-bottom: env(safe-area-inset-bottom, 0px);

        border-radius: 1.75rem;

        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);

        background: rgba(255, 255, 255, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.3);

        box-shadow:
            0 15px 35px -5px rgba(0, 0, 0, 0.1),
            0 5px 15px -3px rgba(0, 0, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);

        transition: all 0.3s ease;
    }

    .dark .fi-bottom-nav {
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.08);

        box-shadow:
            0 20px 40px -10px rgba(0, 0, 0, 0.5),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .fi-bottom-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex: 1;

        gap: 0.35rem;
        padding: 0.5rem 0;

        border: none;
        background: none;
        text-decoration: none;

        cursor: pointer;
        position: relative;

        -webkit-tap-highlight-color: transparent;

        color: var(--gray-500);

        transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dark .fi-bottom-nav-item {
        color: var(--gray-400);
    }

    .fi-bottom-nav-item.fi-active {
        color: var(--primary-600);
        transform: translateY(-2px);
    }

    .fi-bottom-nav-item.fi-active::after {
        content: '';
        position: absolute;
        bottom: 0.25rem;
        width: 1.25rem;
        height: 0.125rem;
        background: currentColor;
        border-radius: 1rem;
        box-shadow: 0 0 8px currentColor;
        transition: all 0.3s ease;
    }

    .dark .fi-bottom-nav-item.fi-active {
        color: var(--primary-400);
    }

    .fi-bottom-nav-label {
        font-size: 0.65rem;
        line-height: 1;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        text-align: center;
        padding: 0 0.25rem;
        transition: all 0.3s ease;
    }

    .fi-bottom-nav-item.fi-active .fi-bottom-nav-label {
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .fi-bottom-nav-icon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .fi-bottom-nav-item.fi-active .fi-bottom-nav-icon-wrapper {
        transform: scale(1.1);
    }

    .fi-bottom-nav-badge {
        position: absolute;
        top: -4px;
        right: -6px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 1rem;
        height: 1rem;
        padding: 0 0.25rem;
        font-size: 0.625rem;
        font-weight: 700;
        color: white;
        border-radius: 9999px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fi-bottom-nav-badge-dot {
        position: absolute;
        top: 0;
        right: -2px;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
        border: 2px solid white;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .dark .fi-bottom-nav-badge-dot {
        border-color: #1f2937;
    }

    @media (min-width: 1024px) {
        .fi-bottom-nav {
            display: none !important;
        }

        .fi-main {
            padding-bottom: 0 !important;
        }
    }

    @media (max-width: 1023px) {
        .fi-main {
            padding-bottom: calc(6rem + env(safe-area-inset-bottom, 0px)) !important;
        }
    }
</style>

<nav x-data="{}" x-show="! $store.sidebar.isOpen" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fi-bottom-nav"
    aria-label="Bottom navigation">
    @foreach ($items as $item)
        @php
            $isActive = $item->isActiveState();
            $icon = $isActive && $item->getActiveIcon() ? $item->getActiveIcon() : $item->getIcon();
            $badge = $item->getBadge();
            $badgeColor = $item->getBadgeColor() ?? 'primary';
            $badgeCssColor = is_string($badgeColor) ? "var(--{$badgeColor}-500)" : 'var(--primary-500)';
        @endphp

        <a {{ generate_href_html($item->getUrl()) }} @class(['fi-bottom-nav-item', 'fi-active' => $isActive])
            @if ($isActive) aria-current="page" @endif>
            <span class="fi-bottom-nav-icon-wrapper">
                {{ generate_icon_html($icon, size: IconSize::Large) }}

                @if ($badge !== null && $badge !== '')
                    @if (is_numeric($badge))
                        <span class="fi-bottom-nav-badge"
                            style="background-color: {{ $badgeCssColor }}">{{ $badge }}</span>
                    @else
                        <span class="fi-bottom-nav-badge-dot" style="background-color: {{ $badgeCssColor }}"></span>
                    @endif
                @endif
            </span>

            <span class="fi-bottom-nav-label">{{ $item->getLabel() }}</span>
        </a>
    @endforeach

    @if ($moreButtonEnabled)
        <button type="button" x-on:click="$store.sidebar.open()" class="fi-bottom-nav-item"
            aria-label="{{ $moreButtonLabel }}">
            <span class="fi-bottom-nav-icon-wrapper">
                {{ generate_icon_html('heroicon-o-bars-3', size: IconSize::Large) }}
            </span>

            <span class="fi-bottom-nav-label">{{ $moreButtonLabel }}</span>
        </button>
    @endif
</nav>
