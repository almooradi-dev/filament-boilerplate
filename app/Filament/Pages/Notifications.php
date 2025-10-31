<?php

namespace App\Filament\Pages;

use App\Mail\GeneralEmail;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class Notifications extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.notifications';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-bell';

    public static function getNavigationGroup(): ?string
    {
        return __('core.administration');
    }

    public function getTitle(): string | Htmlable
    {
        return __('core.notifications');
    }

    public static function getNavigationLabel(): string
    {
        return __('core.notifications');
    }

    public $data = [];

    public $receivers = [];
    public $send_to_all_users = true;
    public ?string $title_en = null;
    public ?string $title_ar = null;
    public ?string $body_en = null;
    public ?string $body_ar = null;

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'email',
            'from_email' => config('mail.from.address'),
        ]);
    }

    public function schema(Schema $schema): Schema
    {
        $users = User::whereActive()->get()->mapWithKeys(function ($user) {
            return [$user->id => $user->full_name];
        })->toArray(); // TODO: Server search for large number of users

        return $schema
            ->components([
                Toggle::make('send_to_all_users')
                    ->label(new HtmlString('Send to all users? <small style="color: gray">(Active users only)</small>'))
                    ->default(true)->live(),
                Select::make('receivers')
                    ->searchable()
                    ->multiple()
                    ->getSearchResultsUsing(function (string $search) use ($users): array {
                        return collect($users)
                            ->filter(function ($name) use ($search) {
                                return str_contains(strtolower($name), strtolower($search));
                            })
                            ->mapWithKeys(function ($name, $id) {
                                return [$id => $name];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(fn($value): ?string => $users[$value])
                    ->optionsLimit(50)
                    ->visible(fn(Get $get) => !$get('send_to_all_users')),
                Grid::make(2)
                    ->schema([
                        Select::make('type')
                            ->live()
                            ->options([
                                'email' => 'Email'
                            ]),
                        TextInput::make('from_email')
                            ->label('From Email')
                            ->email()
                            ->required()
                            ->visible(fn(Get $get) => $get('type') == 'email'),
                    ]),
                Grid::make('title')
                    ->schema([
                        TextInput::make('title_en')->label('Title')->required(),
                        // TextInput::make('title_ar')->label('Title (AR)')->required(),
                    ])->columns(1),
                Grid::make('body')
                    ->schema([
                        RichEditor::make('body_en')->label('Body')->required(), // TODO: Change the uploaded files directory 
                        // Textarea::make('body_en')->label('Body (EN)')->rows(10)->requiredWith('body_ar'),
                        // Textarea::make('body_ar')->label('Body (AR)')->rows(10)->requiredWith('body_en'),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $this->data = $this->form->getState();

        $usersQuery = User::whereActive();
        $users = $this->data['send_to_all_users'] ? $usersQuery->get() : $usersQuery->whereIn('id', $this->data['receivers'])->get();

        if ($this->data['type'] == 'email') {
            foreach ($users as $user) {
                if (!$user->email) {
                    continue;
                }
                Mail::to($user->email)->send(new GeneralEmail([
                    'subject' => $this->data['title_en'],
                    'body' => $this->data['body_en'],
                ], fromEmail: $this->data['from_email']));
            }
        }

        // foreach ($users as $user) {
        //     $user?->sendNotification([
        //         'en' => $this->data['title_en'],
        //         'ar' => $this->data['title_ar'],
        //     ], [
        //         'en' => $this->data['body_en'],
        //         'ar' => $this->data['body_ar'],
        //     ]);
        // }

        Notification::make()
            ->title('Notification sent successfully using "' . $this->data['type'] . '"')
            ->success()
            ->send();
    }

    // public static function canAccess(): bool
    // {
    //     return auth()->user()->can('notification.send');
    // }
}
