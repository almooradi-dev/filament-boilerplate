<?php

namespace App\Filament\Resources\Core;

use App\Filament\Resources\Core\UserResource\Pages;
use App\Models\Core\UserStatus;
use App\Models\User;
use App\Tables\Columns\ColorColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $fullPhoneConcatQuery = "CONCAT(country_code, '-', phone)";

    public static function getNavigationGroup(): ?string
    {
        return __('core.administration');
    }

    public static function getModelLabel(): string
    {
        return __('core.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make('name')
                    ->schema([
                        TextInput::make('first_name')
                            ->label(__('core.first_name'))
                            ->maxLength(125)
                            ->autofocus(),
                        TextInput::make('last_name')
                            ->label(__('core.last_name'))
                            ->maxLength(125),
                    ])->columns(2),
                TextInput::make('email')
                    ->label(__('core.email'))
                    ->unique(ignoreRecord: true)
                    ->email(),

                TextInput::make('password')
                    ->label(__('core.password'))
                    ->password()
                    ->minLength(8)
                    ->reactive()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => !empty($state) ? true : false)
                    ->revealable()
                    ->autocomplete('new-password'), // "False" not working but "new-password" do
                TextInput::make('password_confirmation')
                    ->label(__('core.password_confirmation'))
                    ->password()
                    ->reactive()
                    ->required(fn($get) => !empty($get('password')) ? true : false)
                    ->same('password')
                    ->dehydrated(false)
                    ->revealable()
                    ->autocomplete('new-password'), // "False" not working but "new-password" do

                TextInput::make('country_code')
                    ->label(__('core.country_code'))
                    ->default(961)
                    ->integer(),
                TextInput::make('phone')
                    ->required()
                    ->label(__('core.phone'))
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->unique(ignorable: fn(?Model $record) => $record, modifyRuleUsing: fn(Unique $rule, Get $get) => $rule->where('phone', $get('phone'))->where('country_code', $get('country_code'))),
                Select::make('status_id')
                    ->label(__('core.status'))
                    ->relationship('status', 'name', fn(Builder $query) => $query->whereActive()->orderBy('id', 'asc')),
                Select::make('roles')
                    ->label(__('core.roles'))
                    ->multiple()
                    ->preload()
                    // ->default([getDefaultID('role')])
                    ->relationship('roles', 'name', fn($query) => $query->when(!auth()->user()->hasRole('super_admin'), fn($query) => $query->where('name', '!=', 'super_admin'))),

                FileUpload::make('avatar')
                    ->label(__('core.avatar'))
                    ->moveFiles()
                    ->image()
                    ->directory('users')
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->withGlobalCustomization()
            ->columns([
                TextColumn::make('first_name')->searchable()->sortable()->label(__('core.first_name')),
                TextColumn::make('last_name')->searchable()->sortable()->label(__('core.last_name')),
                TextColumn::make('email')->searchable()->sortable()->label(__('core.email')),
                TextColumn::make('phone')
                    ->formatStateUsing(fn(User $record): string => $record->display_phone)
                    ->searchable(query: fn(Builder $query, string $search) => $query->whereRaw(static::$fullPhoneConcatQuery . ' LIKE ?', ['%' . $search . '%']))
                    ->label(__('core.full_phone')),
                ColorColumn::make('status.color')
                    ->text(fn($record) => $record->status->name)
                    ->label(__('core.color'))
                    ->searchable(query: fn(Builder $query, string $search) => $query->whereHas('status', fn($q) => $q->where('name', 'like', '%' . $search . '%'))),
                TextColumn::make('roles.name')
                    ->searchable()
                    ->label(__('core.roles'))
                // ->searchable(query: fn(Builder $query, string $search) => $query->whereHas('roles', fn($q) => $q->where('name', 'like', '%' . $search . '%'))),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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
