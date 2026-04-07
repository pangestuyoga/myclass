<?php

namespace App\Filament\Pages\System\Changelog;

use App\Enums\ChangelogType;
use App\Filament\Pages\System\Changelog\Actions\CreateChangelogAction;
use App\Filament\Pages\System\Changelog\Actions\DeleteChangelogAction;
use App\Filament\Pages\System\Changelog\Actions\EditChangelogAction;
use App\Filament\Pages\System\Changelog\Actions\MarkAsReadAction;
use App\Filament\Support\SystemNotification;
use App\Models\Changelog;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use UnitEnum;

class Index extends Page implements HasActions, HasForms
{
    use HasPageShield, InteractsWithActions, InteractsWithForms;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $title = 'Changelog';

    protected static ?string $navigationLabel = 'Changelog';

    protected static ?string $slug = 'system/changelog';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.system.changelog.index';

    public static function getNavigationBadge(): ?string
    {
        $unreadCount = Changelog::whereDoesntHave('users', fn ($query) => $query->where('user_id', auth()->id()))->count();

        return $unreadCount > 0 ? (string) $unreadCount : null;
    }

    public static function getPagePermission(): string
    {
        return 'View:Changelog';
    }

    #[Computed]
    public function techStack(): array
    {
        $latestUpdate = Changelog::latest('release_date')->first();

        return [
            'name' => config('app.name'),
            'version' => $latestUpdate?->version ?? 'v1.0.0',
            'stack' => [
                'PHP' => PHP_VERSION,
                'Laravel' => app()->version(),
                'Filament' => 'v5.x',
                'Database' => config('database.default'),
            ],
        ];
    }

    #[Computed]
    public function changelogs(): Collection
    {
        return Changelog::with([
            'users' => fn ($query) => $query->where('user_id', auth()->id()),
        ])
            ->get()
            ->sortBy([
                fn ($a, $b) => $a->is_read <=> $b->is_read,
                ['release_date', 'desc'],
            ])
            ->values();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateChangelogAction::make()
                ->visible(fn () => auth()->user()?->can('Create:Changelog')),
        ];
    }

    public function editChangelogAction(): Action
    {
        return EditChangelogAction::make()
            ->visible(fn () => auth()->user()?->can('Update:Changelog'));
    }

    public function deleteChangelogAction(): Action
    {
        return DeleteChangelogAction::make()
            ->visible(fn () => auth()->user()?->can('Delete:Changelog'));
    }

    public function markAsReadAction(): Action
    {
        return MarkAsReadAction::make();
    }

    #[Computed]
    public function emptyHeading(): string
    {
        return SystemNotification::getByKey('labels.empty_changelog.title');
    }

    #[Computed]
    public function emptyDescription(): string
    {
        return SystemNotification::getByKey('labels.empty_changelog.description');
    }

    public function changelogFormSchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    TextInput::make('version')
                        ->label('Versi')
                        ->placeholder('v1.0.0')
                        ->required()
                        ->unique(ignoreRecord: true),

                    DatePicker::make('release_date')
                        ->label('Tanggal Rilis')
                        ->required()
                        ->native(false)
                        ->default(now())
                        ->displayFormat('d F Y'),

                    Select::make('type')
                        ->label('Tipe Update')
                        ->options(ChangelogType::class)
                        ->required()
                        ->native(false),
                ]),

            TextInput::make('title')
                ->label('Judul Update')
                ->placeholder('Sistem Notifikasi & Estetika Baru')
                ->required()
                ->maxLength(100)
                ->autocomplete(false),

            TagsInput::make('changes')
                ->label('Daftar Perubahan')
                ->placeholder('Tambah perubahan...')
                ->required(),

            RichEditor::make('description')
                ->label('Deskripsi')
                ->placeholder('...')
                ->required(),
        ];
    }
}
