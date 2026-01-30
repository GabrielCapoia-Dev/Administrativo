<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\PedidoHistorico;
use App\Models\TipoStatus;
use App\Models\TipoManutencao;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PedidoService
{
    protected TipoStatusService $tipoStatusService;

    public function __construct(TipoStatusService $tipoStatusService)
    {
        $this->tipoStatusService = $tipoStatusService;
    }

    /** Verifica se é admin */
    public function ehAdmin(?User $user = null): bool
    {
        return Gate::allows('admin-only', $user);
    }

    /** Verifica se usuário pode gerenciar pedidos (admin ou servidor educação) */
    public function podeGerenciarPedidos(?User $user = null): bool
    {
        if (!$user) return false;
        return $this->ehAdmin($user) || $user->hasPermissionTo('Editar Pedidos');
    }

    /** Verifica se usuário pode criar pedidos */
    public function podeCriarPedidos(?User $user = null): bool
    {
        if (!$user) return false;
        return $user->hasPermissionTo('Criar Pedidos');
    }

    /** Conta pedidos não lidos/novos para badge */
    public function contarPedidosNovos(?User $user): ?string
    {
        if (!$this->podeGerenciarPedidos($user)) return null;

        $statusInicial = $this->tipoStatusService->obterStatusInicial();
        if (!$statusInicial) return null;

        $query = Pedido::where('ativo', true)
            ->where('tipo_status_id', $statusInicial->id);

        // Se não for admin, filtra por escola
        if (!$this->ehAdmin($user) && $user?->id_escola) {
            $query->where('escola_id', $user->id_escola);
        }

        $count = $query->count();
        return $count > 0 ? (string) $count : null;
    }

    /** Query base filtrada por perfil */
    public function queryPorPerfil(Builder $query, ?User $user): Builder
    {
        if (!$user) return $query->whereRaw('1 = 0');

        $query->where('ativo', true);

        // Admin vê todos
        if ($this->ehAdmin($user)) {
            return $query;
        }

        // Usuário vinculado a escola só vê pedidos da escola
        if ($user->id_escola) {
            $query->where('escola_id', $user->id_escola);
        }

        return $query;
    }

    /** Cria pedido com status inicial e histórico */
    public function criarPedido(array $data, User $solicitante): Pedido
    {
        $statusInicial = $this->tipoStatusService->obterStatusInicial();

        $pedido = Pedido::create([
            ...$data,
            'solicitante_id' => $solicitante->id,
            'escola_id' => $solicitante->id_escola,
            'tipo_status_id' => $statusInicial->id,
        ]);

        // Cria primeiro histórico
        $this->registrarHistorico($pedido, $statusInicial, $solicitante, 'Pedido criado');

        return $pedido;
    }

    /** Altera status e registra histórico */
    public function alterarStatus(Pedido $pedido, TipoStatus $novoStatus, User $responsavel, ?string $observacao = null): void
    {
        $pedido->update(['tipo_status_id' => $novoStatus->id]);

        if ($novoStatus->finaliza_pedido) {
            $pedido->update(['data_entrega' => now()]);
        }

        if ($novoStatus->cancela_pedido) {
            $pedido->update(['data_cancelamento' => now()]);
        }

        $this->registrarHistorico($pedido, $novoStatus, $responsavel, $observacao);
    }

    /** Registra entrada no histórico */
    public function registrarHistorico(Pedido $pedido, TipoStatus $status, User $user, ?string $observacao = null): PedidoHistorico
    {
        return PedidoHistorico::create([
            'pedido_id' => $pedido->id,
            'tipo_status_id' => $status->id,
            'user_id' => $user->id,
            'observacao' => $observacao,
            'notificacao_enviada' => false,
        ]);
    }

    /** FORM: Criação de pedido */
    public function configurarFormularioCriacao(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dados do Pedido')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Forms\Components\Select::make('tipo_manutencao_id')
                        ->label('Tipo de Manutenção')
                        ->options(TipoManutencao::where('ativo', true)->pluck('nome', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição do Problema')
                        ->required()
                        ->rows(5)
                        ->maxLength(2000)
                        ->helperText('Descreva detalhadamente o problema encontrado')
                        ->columnSpanFull(),

                    Forms\Components\DatePicker::make('data_prevista')
                        ->label('Data Desejada')
                        ->helperText('Quando você gostaria que fosse resolvido?')
                        ->minDate(now()),
                ])
                ->columns(2),

            Forms\Components\Section::make('Fotos')
                ->icon('heroicon-o-photo')
                ->schema([
                    Forms\Components\FileUpload::make('fotos')
                        ->label('Fotos do Problema')
                        ->multiple()
                        ->image()
                        ->maxFiles(10)
                        ->maxSize(5120)
                        ->directory('pedidos/fotos')
                        ->helperText('Adicione até 10 fotos para ilustrar o problema (máx. 5MB cada)')
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    /** FORM: Edição/Gestão de pedido */
    public function configurarFormularioGestao(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações do Pedido')
                ->icon('heroicon-o-information-circle')
                ->schema([
                    Forms\Components\TextInput::make('numero_protocolo')
                        ->label('Protocolo')
                        ->disabled(),

                    Forms\Components\Select::make('tipo_manutencao_id')
                        ->label('Tipo de Manutenção')
                        ->options(TipoManutencao::where('ativo', true)->pluck('nome', 'id'))
                        ->disabled(),

                    Forms\Components\Placeholder::make('escola_nome')
                        ->label('Escola')
                        ->content(fn(?Pedido $record) => $record?->escola?->nome ?? '-'),

                    Forms\Components\Placeholder::make('solicitante_nome')
                        ->label('Solicitante')
                        ->content(fn(?Pedido $record) => $record?->solicitante?->name ?? '-'),

                    Forms\Components\Textarea::make('descricao')
                        ->label('Descrição')
                        ->disabled()
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Gestão')
                ->icon('heroicon-o-cog-6-tooth')
                ->schema([
                    Forms\Components\Select::make('tipo_status_id')
                        ->label('Status')
                        ->options(TipoStatus::where('ativo', true)->orderBy('ordem')->pluck('nome', 'id'))
                        ->required(),

                    Forms\Components\Select::make('responsavel_educacao_id')
                        ->label('Responsável Educação')
                        ->options(
                            User::whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'Secretario']))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('responsavel_obras_id')
                        ->label('Responsável Obras')
                        ->options(
                            User::whereHas('roles', fn($q) => $q->where('name', 'Servidor Obras'))
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->preload(),

                    Forms\Components\DatePicker::make('data_prevista')
                        ->label('Data Prevista'),

                    Forms\Components\Textarea::make('observacao_status')
                        ->label('Observação')
                        ->helperText('Será registrada no histórico')
                        ->rows(3)
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    /** TABLE: Lista de pedidos */
    public function configurarTabela(Table $table, ?User $user): Table
    {
        return $table
            ->columns($this->colunasTabela($user))
            ->filters($this->filtrosTabela())
            ->actions($this->acoesTabela($user))
            ->bulkActions($this->acoesEmMassa($user))
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    protected function colunasTabela(?User $user): array
    {
        $colunas = [
            Tables\Columns\TextColumn::make('numero_protocolo')
                ->label('Protocolo')
                ->searchable()
                ->sortable()
                ->copyable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('tipoManutencao.nome')
                ->label('Tipo')
                ->sortable()
                ->badge()
                ->color('gray'),

            Tables\Columns\TextColumn::make('tipoStatus.nome')
                ->label('Status')
                ->sortable()
                ->badge()
                ->color(fn(Pedido $record) => $this->corStatus($record->tipoStatus)),

            Tables\Columns\TextColumn::make('descricao')
                ->label('Descrição')
                ->limit(40)
                ->wrap()
                ->toggleable(),
        ];

        // Admin vê escola
        if ($this->ehAdmin($user)) {
            $colunas[] = Tables\Columns\TextColumn::make('escola.nome')
                ->label('Escola')
                ->searchable()
                ->sortable()
                ->wrap();
        }

        $colunas[] = Tables\Columns\TextColumn::make('solicitante.name')
            ->label('Solicitante')
            ->sortable()
            ->toggleable();

        $colunas[] = Tables\Columns\TextColumn::make('created_at')
            ->label('Criado em')
            ->dateTime('d/m/Y H:i')
            ->sortable();

        $colunas[] = Tables\Columns\TextColumn::make('data_prevista')
            ->label('Previsão')
            ->date('d/m/Y')
            ->sortable()
            ->toggleable();

        return $colunas;
    }

    protected function filtrosTabela(): array
    {
        return [
            Tables\Filters\SelectFilter::make('tipo_status_id')
                ->label('Status')
                ->options(TipoStatus::where('ativo', true)->orderBy('ordem')->pluck('nome', 'id')),

            Tables\Filters\SelectFilter::make('tipo_manutencao_id')
                ->label('Tipo')
                ->options(TipoManutencao::where('ativo', true)->pluck('nome', 'id')),
        ];
    }

    protected function acoesTabela(?User $user): array
    {
        $acoes = [
            Tables\Actions\ViewAction::make(),
        ];

        if ($this->podeGerenciarPedidos($user)) {
            $acoes[] = Tables\Actions\EditAction::make();

            $acoes[] = Tables\Actions\Action::make('alterarStatus')
                ->label('Status')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('tipo_status_id')
                        ->label('Novo Status')
                        ->options(TipoStatus::where('ativo', true)->orderBy('ordem')->pluck('nome', 'id'))
                        ->required(),

                    Forms\Components\Textarea::make('observacao')
                        ->label('Observação')
                        ->rows(3),
                ])
                ->action(function (Pedido $record, array $data) use ($user) {
                    $novoStatus = TipoStatus::find($data['tipo_status_id']);
                    $this->alterarStatus($record, $novoStatus, $user, $data['observacao'] ?? null);

                    Notification::make()
                        ->success()
                        ->title('Status alterado')
                        ->send();
                });
        }

        return $acoes;
    }

    protected function acoesEmMassa(?User $user): array
    {
        if (!$this->podeGerenciarPedidos($user)) return [];

        return [
            Tables\Actions\BulkAction::make('alterarStatusEmMassa')
                ->label('Alterar Status')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    Forms\Components\Select::make('tipo_status_id')
                        ->label('Novo Status')
                        ->options(TipoStatus::where('ativo', true)->orderBy('ordem')->pluck('nome', 'id'))
                        ->required(),

                    Forms\Components\Textarea::make('observacao')
                        ->label('Observação')
                        ->rows(3),
                ])
                ->action(function ($records, array $data) use ($user) {
                    $novoStatus = TipoStatus::find($data['tipo_status_id']);
                    foreach ($records as $pedido) {
                        $this->alterarStatus($pedido, $novoStatus, $user, $data['observacao'] ?? null);
                    }

                    Notification::make()
                        ->success()
                        ->title('Status alterado em ' . $records->count() . ' pedidos')
                        ->send();
                }),
        ];
    }

    protected function corStatus(?TipoStatus $status): string
    {
        if (!$status) return 'gray';
        if ($status->finaliza_pedido) return 'success';
        if ($status->cancela_pedido) return 'danger';

        // Mapeia cores hex para cores do Filament
        $mapa = [
            '#3B82F6' => 'info',      // blue
            '#F59E0B' => 'warning',   // amber
            '#8B5CF6' => 'primary',   // violet
            '#06B6D4' => 'info',      // cyan
            '#10B981' => 'success',   // emerald
            '#EF4444' => 'danger',    // red
        ];

        return $mapa[$status->cor] ?? 'gray';
    }
    public function ehServidorEducacao(User $user): bool
    {
        return is_null($user->id_escola) && $user->hasRole('Servidor Educação');
    }
}
