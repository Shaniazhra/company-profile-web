<?php

namespace App\Filament\Resources\PipelineSteps\Pages;

use App\Filament\Resources\PipelineSteps\PipelineStepResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePipelineStep extends CreateRecord
{
    use Translatable;

    protected static string $resource = PipelineStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}