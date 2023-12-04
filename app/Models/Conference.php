<?php

namespace App\Models;

use Filament\Forms;
use App\Enums\Region;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [

            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Conference Details')
                        ->schema([
                            TextInput::make('name')
                                ->label('Conference Name')
                                ->prefix('https://')
                                ->prefixIcon('heroicon-o-globe-alt')
                                ->suffix('.com')
                                ->required()
                                ->maxLength(60)
                                ->columnSpanFull(),
                            MarkdownEditor::make('descriotion')
                                ->columnSpanFull()
                                ->required()
                                ->maxLength(255),
                            DateTimePicker::make('start_date')
                                ->native(false)
                                ->displayFormat('Y M d D h:m:s a')
                                ->required(),
                            DateTimePicker::make('end_date')
                                ->native(false)
                                ->displayFormat('d M Y h:m:s a')
                                ->required(),

                            Fieldset::make('status')
                                ->columns(1)
                                ->schema([
                                    Select::make('status')

                                        ->options([
                                            'draft' => 'Draft',
                                            'published' => 'Published',
                                            'archived' => 'Archived'
                                        ])
                                        ->required(),
                                    Toggle::make('is_published')
                                        ->default(true),
                                ]),
                        ]),

                    Tabs\Tab::make('Location')
                        ->schema([
                            Select::make('region')
                                ->live()
                                ->enum(Region::class)
                                ->options(Region::class),
                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->createOptionForm(Venue::getForm())
                                ->editOptionForm(Venue::getForm())
                                ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                    return $query->where('region', $get('region'));
                                }),

                            Fieldset::make('Speakers')
                                ->columns(1)
                                ->schema([
                                    CheckboxList::make('speakers')
                                        ->columnSpanFull()
                                        ->columns(3)
                                        ->searchable()
                                        ->bulkToggleable()
                                        ->relationship('speakers', 'name')
                                        ->options(
                                            Speaker::all()->pluck('name', 'id')
                                        ),
                                ]),
                        ]),
                ]),

            Actions::make([
                Action::make('Fill data with Factory')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }
                        if (!app()->environment('local')) {
                            return false;
                        }
                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ]),

        ];
    }
}
