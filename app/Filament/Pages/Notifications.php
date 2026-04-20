<?php

namespace App\Filament\Pages;

use App\Mail\GeneralEmail;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
    use InteractsWithForms, HasPageShield;

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

    function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Toggle::make('send_to_all_users')
                    ->label(new HtmlString('Send to all users? <small style="color: gray">(Active users only)</small>'))
                    ->default(true)
                    ->live(),
                Select::make('receivers')
                    ->searchable()
                    ->multiple()
                    ->getSearchResultsUsing(function (string $search): array {
                        return User::query()
                            ->whereActive()
                            ->where(function ($query) use ($search) {
                                $query->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn($user) => [
                                $user->id => "{$user->full_name} ({$user->email})"
                            ])
                            ->toArray();
                    })
                    ->getOptionLabelsUsing(function (array $values): array {
                        return User::whereIn('id', $values)
                            ->get()
                            ->mapWithKeys(fn($user) => [
                                $user->id => "{$user->full_name} ({$user->email})"
                            ])
                            ->toArray();
                    })
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
                TagsInput::make('external_emails')
                    ->rules(['array'])
                    ->nestedRecursiveRules(['email', 'max:100'])
                    ->visible(fn(Get $get) => $get('type') == 'email'),
                Grid::make(1)
                    ->schema([
                        TextInput::make('title_en')->label('Title')->required(),
                        // TextInput::make('title_ar')->label('Title (AR)')->required(),
                    ]),
                Grid::make(1)
                    ->schema([
                        RichEditor::make('body_en')
                            ->label('Body')
                            ->required()
                            ->fileAttachmentsDirectory('notifications/attachments'),
                        // Textarea::make('body_en')->label('Body (EN)')->rows(10)->requiredWith('body_ar'),
                        // Textarea::make('body_ar')->label('Body (AR)')->rows(10)->requiredWith('body_en'),
                    ]),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $this->data = $this->form->getState();

        $usersQuery = User::whereActive();
        $users = $this->data['send_to_all_users'] ? $usersQuery->get() : $usersQuery->whereIn('id', $this->data['receivers'])->get();

        if ($this->data['type'] == 'email') {
            $emails = array_merge($users->pluck('email')->toArray(), $this->data['external_emails'] ?? []);

            foreach ($emails as $toEmail) {
                if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }

                $this->sendEmail(
                    $this->data['title_en'],
                    $this->data['body_en'],
                    $toEmail,
                    $this->data['from_email']
                );
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

    /**
     * Send email to single email
     *
     * @param string $title
     * @param string $body
     * @param string $toEmail
     * @param string $fromEmail
     * @return void
     */
    private function sendEmail(string $title, string $body, string $toEmail, string $fromEmail)
    {
        Mail::to($toEmail)->send(new GeneralEmail([
            'subject' => $title,
            'body' => $body,
        ], fromEmail: $fromEmail));
    }
}
