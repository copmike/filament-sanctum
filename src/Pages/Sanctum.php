<?php

namespace EightyGrit\FilamentSanctum\Pages;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;

class Sanctum extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static string $view = 'filament-sanctum::pages.sanctum';

    public static function getSlug(): string
    {
        return config('filament-sanctum.slug');
    }

    public function getTitle(): string
    {
        return trans(config('filament-sanctum.label'));
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-sanctum.navigation.should_register', true)
            ? config('filament-sanctum.navigation.group', null)
            : '';
    }

    public static function getNavigationSort(): int
    {
        return config('filament-sanctum.navigation.sort', -1);
    }

    public static function getNavigationLabel(): string
    {
        return trans(config('filament-sanctum.label'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('filament-sanctum.navigation_menu');
    }

    protected function getTableQuery(): Builder
    {
        return Auth::user()->tokens()->getQuery();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->defaultSort('id', 'desc')
            ->columns($this->getTableColumns())
            ->bulkActions([
                Tables\Actions\BulkAction::make('revoke')
                    ->label(trans('Revoke'))
                    ->action(fn(Collection $records) => $records->each->delete())
                    ->deselectRecordsAfterCompletion()
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash'),
            ]);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(trans('Name'))
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('abilities')
                ->badge()
                ->label(trans('Abilities')),
            Tables\Columns\TextColumn::make('last_used_at')
                ->label(trans('Last used at'))
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(trans('Created at'))
                ->dateTime()
                ->sortable(),
        ];
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make('new')
                ->label(trans('Create a new Token'))
                ->action(function (array $data) {
                    
                    $user = Auth::user();
                    
                    $token = $user->createToken($data['name'], $data['abilities'])->plainTextToken;
                    
                    request()->session()->flash('sanctum-token', $token);
                    
                    Notification::make()
                        ->title(trans('Saved successfully'))
                        ->success()
                        ->icon('heroicon-o-finger-print')
                        ->title(trans('Token was created successfully'))
                        ->send();

                    return redirect(static::getUrl());
                })
                ->form([
                    Forms\Components\TextInput::make('name')
                        ->label(trans('Token Name'))
                        ->required(),
                    Forms\Components\CheckboxList::make('abilities')
                        ->label(trans('Abilities'))
                        ->options(config('filament-sanctum.abilities'))
                        ->columns(config('filament-sanctum.columns')),
                ]),
        ];
    }
}
