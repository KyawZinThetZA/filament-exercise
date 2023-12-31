<?php

namespace App\Models;

use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    const QUALIFICATIONS = [
        'business-leader' => 'Business Leader',
        'charisma' => 'Charismatic Speaker',
        'first-time' => 'First Time Speaker',
        'hometown-hero' => 'Hometown Hero',
        'humanitarian' => 'Works in Humanitarian Field',
        'laracasts-contributor' => 'Laracasts Contributor',
        'twitter-influencer' => 'Large Twitter Following',
        'youtube-influencer' => 'Large YouTube Following',
        'open-source' => 'Open Source Creator / Maintainer',
        'unique-perspective' => 'Unique Perspective',
    ];

    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany
    {
        return $this->hasMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            FileUpload::make('avatar')
                ->image()
                ->avatar()
                ->imageEditor()
                ->directory('avatar')
                ->maxSize(1024 * 1024 * 2),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            TextInput::make('twitter_handle')
                ->maxLength(255),
            Textarea::make('bio')
                ->columnSpanFull(),
            CheckboxList::make('qualification')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options([
                    'business-leader' => 'Business Leader',
                    'charisma' => 'Charismatic Speaker',
                    'first-time' => 'First Time Speaker',
                    'hometown-hero' => 'Hometown Hero',
                    'humanitarian' => 'Works in Humanitarian Field',
                    'laracasts-contributor' => 'Laracasts Contributor',
                    'twitter-influencer' => 'Large Twitter Following',
                    'youtube-influencer' => 'Large YouTube Following',
                    'open-source' => 'Open Source Creator / Maintainer',
                    'unique-perspective' => 'Unique Perspective',
                ])
                ->options(self::QUALIFICATIONS)
                ->descriptions([
                    'business-leader' => 'Business Leader ehvo ocie',
                    'charisma' => 'Charismatic Speaker jfiekie',
                ])
                ->columns(3),
        ];
    }
}
