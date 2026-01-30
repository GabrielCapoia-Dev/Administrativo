<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EscolaResource\Pages;
use App\Filament\Resources\EscolaResource\RelationManagers;
use App\Models\Escola;
use App\Services\EscolaService as Service;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EscolaResource extends Resource
{
    protected static ?string $model = Escola::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $modelLabel = 'Escola';
    protected static ?string $pluralModelLabel = 'Escolas';
    protected static ?string $navigationGroup = 'Cadastros';
    protected static ?string $slug = 'escolas';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return app(Service::class)->configurarFormulario($form);
    }

    public static function table(Table $table): Table
    {
        return app(Service::class)->configurarTabela($table, Auth::user());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsuariosRelationManager::class,
            RelationManagers\PedidosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEscolas::route('/'),
            'create' => Pages\CreateEscola::route('/create'),
            'view' => Pages\ViewEscola::route('/{record}'),
            'edit' => Pages\EditEscola::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return app(Service::class)->listarEscolasQuery(
            parent::getEloquentQuery(),
            Auth::user()
        );
    }
}