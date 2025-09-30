<?php

namespace App\Filament\Resources\Core;

use App\Filament\Resources\Core\PageResource\Pages;
use App\Models\Core\Page;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    use Translatable;

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('core.administration');
    }

    public static function getModelLabel(): string
    {
        return __('core.page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('core.the_pages');
    }

    public static function form(Form $form): Form
    {
        $contentKeyInput = TextInput::make('key')
            ->required()
            ->helperText('A unique key used to indentify this block in the page');
        $contentInfoWithInputs = [
            Grid::make(3)
                ->schema([
                    $contentKeyInput,
                    TextInput::make('title')
                        ->required(),
                    TextInput::make('subtitle'),
                ]),
            Textarea::make('description'),
            TableRepeater::make('buttons')
                ->addActionLabel(__('core.add'))
                ->headers([
                    Header::make('button_label')->label('Label'),
                    Header::make('button_link')->label('Link'),
                    Header::make('button_icon')->label('Icon'),
                    Header::make('button_type')->label('Type'),
                ])
                ->schema([
                    TextInput::make('label'),
                    TextInput::make('link'),
                    TextInput::make('icon'),
                    Select::make('type')
                        ->options([
                            'primary' => 'Primary',
                            'secondary' => 'secondary',
                        ])
                ])
                ->columns(2),
            TextInput::make('quote'),
            TextInput::make('quote_author'),
        ];

        return $form
            ->schema([
                Tabs::make()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('core.name'))
                                    ->required(),
                                TextInput::make('key')
                                    ->label(__('core.key'))
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                Toggle::make('is_active')
                                    ->label(__('core.is_active'))
                                    ->inline(false),
                            ])
                            ->columns(2),
                        Tab::make('Metadata')
                            ->schema([]),
                        Tab::make('content')
                            ->label(__('core.content'))
                            ->schema([
                                Builder::make('content')
                                    ->label('')
                                    ->addActionLabel(__('core.add'))
                                    ->blocks([
                                        // Heading
                                        Builder\Block::make('heading')
                                            ->schema([
                                                $contentKeyInput,
                                                TextInput::make('content')
                                                    ->label('Heading')
                                                    ->required(),
                                                Select::make('level')
                                                    ->options([
                                                        'h1' => 'Heading 1',
                                                        'h2' => 'Heading 2',
                                                        'h3' => 'Heading 3',
                                                        'h4' => 'Heading 4',
                                                        'h5' => 'Heading 5',
                                                        'h6' => 'Heading 6',
                                                    ])
                                                    ->required(),
                                            ])
                                            ->columns(2),
                                        // Paragraph
                                        Builder\Block::make('paragraph')
                                            ->schema([
                                                $contentKeyInput,
                                                Textarea::make('content')
                                                    ->label('Paragraph')
                                                    ->required(),
                                            ]),
                                        // Highlight cards
                                        Builder\Block::make('highlight_cards')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        $contentKeyInput,
                                                        TextInput::make('title'),
                                                        TextInput::make('subtitle'),
                                                    ]),
                                                TextInput::make('description')
                                                    ->columnSpanFull(),
                                                TableRepeater::make('items')
                                                    ->label('')
                                                    ->addActionLabel(__('core.add'))
                                                    ->headers([
                                                        Header::make('label'),
                                                        Header::make('value'),
                                                        Header::make('icon')->width('200px'),
                                                        Header::make('color')->width('150px'),
                                                    ])
                                                    ->schema([
                                                        TextInput::make('label')
                                                            ->label('Label')
                                                            ->required(),
                                                        TextInput::make('value')
                                                            ->label('Value')
                                                            ->required(),
                                                        TextInput::make('icon'),
                                                        ColorPicker::make('color'),
                                                    ])
                                                    ->minItems(1)
                                                    ->columnSpanFull()
                                                    ->columns(2),
                                            ])
                                            ->columns(2),
                                        // Info with images
                                        Builder\Block::make('info_with_images')
                                            ->schema([
                                                ...$contentInfoWithInputs,
                                                FileUpload::make('images') // TODO: Delete removed images
                                                    ->multiple()
                                                    ->image()
                                                    ->imageEditor()
                                                    ->downloadable()
                                                    ->directory('pages')
                                                    ->panelLayout('grid'),
                                                Select::make('images_position')
                                                    ->options([
                                                        'left' => 'Left',
                                                        'right' => 'Right',
                                                    ])
                                                    ->default('left'),
                                            ])
                                            ->columns(2),
                                        // Info with list
                                        Builder\Block::make('info_with_list')
                                            ->schema([
                                                ...$contentInfoWithInputs,
                                                TableRepeater::make('items')
                                                    ->addActionLabel(__('core.add'))
                                                    ->headers([
                                                        Header::make('label'),
                                                        Header::make('value'),
                                                        Header::make('icon')->width('200px'),
                                                        Header::make('color')->width('150px'),
                                                    ])
                                                    ->schema([
                                                        TextInput::make('label'),
                                                        TextInput::make('value'),
                                                        TextInput::make('icon'),
                                                        ColorPicker::make('color'),
                                                    ])
                                                    ->minItems(1)
                                                    ->columnSpanFull()
                                                    ->columns(2),
                                            ])
                                            ->columns(2),
                                        // Slider
                                        Builder\Block::make('slider')
                                            ->schema([
                                                Grid::make(3)
                                                    ->schema([
                                                        $contentKeyInput,
                                                        TextInput::make('title')
                                                            ->required(),
                                                        TextInput::make('subtitle'),
                                                    ]),
                                                TableRepeater::make('slides')
                                                    ->addActionLabel(__('core.add'))
                                                    ->headers([
                                                        Header::make('slide_media')->label('Media')->width('200px'),
                                                        Header::make('slide_title')->label('Title'),
                                                        Header::make('slide_description')->label('Description'),
                                                        Header::make('slide_button')->label('Button'),
                                                    ])
                                                    ->schema([
                                                        FileUpload::make('slide_media') // TODO: Delete removed images
                                                            ->image()
                                                            ->imageEditor()
                                                            ->downloadable()
                                                            ->directory('pages'),
                                                        TextInput::make('slide_title'),
                                                        TextInput::make('slide_description'),
                                                        // Button
                                                        Grid::make(2)
                                                            ->schema([
                                                                TextInput::make('slide_button_label')->label('Label'),
                                                                TextInput::make('slide_button_link')->url()->label('Link'),
                                                                TextInput::make('slide_button_icon')->label('Icon'),
                                                                Select::make('slide_button_type')
                                                                    ->options([
                                                                        'primary' => 'Primary',
                                                                        'secondary' => 'secondary',
                                                                    ])
                                                                    ->label('Type')
                                                            ]),
                                                    ])
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->withGlobalCustomization()
            ->columns([
                TextColumn::make('name')->label(__('core.name')),
                ToggleColumn::make('is_active')->label(__('core.is_active')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
