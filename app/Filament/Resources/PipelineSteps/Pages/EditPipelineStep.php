<?php

namespace App\Filament\Resources\PipelineSteps\Pages;

use App\Filament\Resources\PipelineSteps\PipelineStepResource;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPipelineStep extends EditRecord
{
    use Translatable;

    protected static string $resource = PipelineStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            // tambahkan action lain (DeleteAction, dll) sesuai file aslinya
        ];
    }
}