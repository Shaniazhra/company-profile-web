<?php

namespace App\Filament\Resources\PipelineSteps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PipelineStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('Jenis Alur')
                ->options([
                    'traditional' => 'Cara Tradisional',
                    'dream_ratio' => 'Cara Dream Ratio',
                ])
                ->required(),

            TextInput::make('title')
                ->label('Nama Langkah')
                ->required()
                ->maxLength(100),

            TagsInput::make('sub_points')
                ->label('Poin Detail')
                ->placeholder('Ketik poin lalu tekan Enter')
                ->helperText('Contoh: 3D, Timing, Voice. Kosongkan jika tidak ada.'),

            TextInput::make('sort_order')
                ->label('Urutan')
                ->numeric()
                ->default(0)
                ->required(),

            Toggle::make('is_published')
                ->label('Tampilkan di website')
                ->default(true),
        ]);
    }
}