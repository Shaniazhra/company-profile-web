<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Website')->schema([
                TextInput::make('site_name')
                    ->label('Nama Website')
                    ->required()
                    ->maxLength(100),

                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('site')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->helperText('Disarankan PNG transparan, lebar minimal 400px, maksimal 2 MB.'),

                FileUpload::make('favicon')
                    ->label('Favicon (ikon tab browser)')
                    ->image()
                    ->disk('public')
                    ->directory('site')
                    ->visibility('public')
                    ->maxSize(512)
                    ->helperText('Gambar persegi, PNG 512x512 disarankan, maksimal 512 KB.'),
            ]),

            Section::make('Teks Website')
                ->description('Isi tiap bahasa lewat tombol ID/EN di kanan atas. Ketik ulang teksnya di tiap bahasa.')
                ->schema([
                    TextInput::make('tagline')
                        ->label('Tagline')
                        ->maxLength(150),

                    Textarea::make('heart_statement')
                        ->label('Pernyataan "The Heart of Dream Ratio"')
                        ->rows(3),

                    Textarea::make('impact_statement')
                        ->label('Pernyataan "The Impact"')
                        ->rows(4),

                    Textarea::make('about_text')
                        ->label('Tentang Kami')
                        ->rows(6),
                ]),

            Section::make('Kontak dan Sosial Media')->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email(),

                TextInput::make('instagram_url')
                    ->label('Link Instagram')
                    ->url()
                    ->placeholder('https://www.instagram.com/dream_ratio'),

                TextInput::make('linkedin_url')
                    ->label('Link LinkedIn')
                    ->url()
                    ->placeholder('https://www.linkedin.com/company/...'),
            ]),
        ]);
    }
}