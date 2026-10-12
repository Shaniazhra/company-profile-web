<?php

namespace App\Filament\Resources\PipelineSteps;

use App\Filament\Resources\PipelineSteps\Pages\CreatePipelineStep;
use App\Filament\Resources\PipelineSteps\Pages\EditPipelineStep;
use App\Filament\Resources\PipelineSteps\Pages\ListPipelineSteps;
use App\Filament\Resources\PipelineSteps\Schemas\PipelineStepForm;
use App\Filament\Resources\PipelineSteps\Tables\PipelineStepsTable;
use App\Models\PipelineStep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use UnitEnum;

class PipelineStepResource extends Resource
{
    use Translatable;

    protected static ?string $model = PipelineStep::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static string|UnitEnum|null $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Alur Produksi';

    protected static ?string $modelLabel = 'Langkah Produksi';

    protected static ?string $pluralModelLabel = 'Alur Produksi';

    public static function form(Schema $schema): Schema
    {
        return PipelineStepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PipelineStepsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPipelineSteps::route('/'),
            'create' => CreatePipelineStep::route('/create'),
            'edit' => EditPipelineStep::route('/{record}/edit'),
        ];
    }
}