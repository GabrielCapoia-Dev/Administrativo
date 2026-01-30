<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoManutencaoResource\Pages;
use App\Models\TipoManutencao;
use App\Services\TipoManutencaoService as Service;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TipoManutencaoResource extends Resource
{
    protected static ?string $model = TipoManutencao::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $modelLabel = 'Tipo de Manutenção';
    protected static ?string $pluralModelLabel = 'Tipos de Manutenção';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $slug = 'tipo-manutencao';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return app(Service::class)->configurarFormulario($form);
    }

    public static function table(Table $table): Table
    {
        return app(Service::class)->configurarTabela($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoManutencao::route('/'),
            'create' => Pages\CreateTipoManutencao::route('/create'),
            'edit' => Pages\EditTipoManutencao::route('/{record}/edit'),
        ];
    }
}