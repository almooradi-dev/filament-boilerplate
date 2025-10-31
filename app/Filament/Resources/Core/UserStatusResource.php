<?php

namespace App\Filament\Resources\Core;

use App\Filament\Resources\Core\UserStatusResource\Pages;
use App\Models\Core\UserStatus;
use App\Tables\Columns\ColorColumn;
use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class UserStatusResource extends Resource
{
    use Translatable;

    protected static ?string $model = UserStatus::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): ?string
    {
        return __('core.administration');
    }

    public static function getModelLabel(): string
    {
        return __('core.user_status');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.user_statuses');
    }

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('core.name'))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, $livewire) {
                        if ($livewire->activeLocale === 'en') {
                            $set('key', str_replace('-', '_', Str::slug($state)));
                        }
                    })
                    ->required(),
                TextInput::make('key')
                    ->label(__('core.key'))
                    ->required()
                    ->readOnly(),
                ColorPicker::make('color')
                    ->label(__('core.color'))
                    ->required(),
                Toggle::make('is_active')
                    ->label(__('core.is_active'))
                    ->inline(false)
                    ->default(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->withGlobalCustomization()
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->label(__('core.name')),
                TextColumn::make('key')->searchable()->sortable()->label(__('core.key')),
                ColorColumn::make('color')
                    ->label(__('core.color')),
                ToggleColumn::make('is_active')->label(__('core.is_active'))
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserStatuses::route('/'),
            'create' => Pages\CreateUserStatus::route('/create'),
            'edit' => Pages\EditUserStatus::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
