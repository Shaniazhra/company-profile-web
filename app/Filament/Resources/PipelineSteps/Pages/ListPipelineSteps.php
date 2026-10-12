<?php

namespace App\Filament\Resources\PipelineSteps\Pages;

use App\Filament\Resources\PipelineSteps\PipelineStepResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPipelineSteps extends ListRecords
{
    use Translatable;

    protected static string $resource = PipelineStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}