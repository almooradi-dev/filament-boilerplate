<?php

namespace App\Filament\Resources\Core;

use App\Filament\Resources\Core\UserStatusResource\Pages;
use App\Models\Core\UserStatus;
use App\Tables\Columns\ColorColumn;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserStatusResource extends Resource
{
    use Translatable;

    protected static ?string $model = UserStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('key')
                    ->required(),
                ColorPicker::make('color')
                    ->required(),
                Toggle::make('is_active')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
