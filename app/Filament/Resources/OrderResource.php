<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Vendas & Financeiro';

    protected static ?int $navigationSort = 1;

    protected static ?string $label = 'Pedido';

    protected static ?string $pluralLabel = 'Pedidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Cliente')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record !== null),
                    
                Forms\Components\TextInput::make('order_number')
                    ->label('Número do Pedido')
                    ->disabled()
                    ->dehydrated(false),
                    
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                    
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'refunded' => 'Reembolsado',
                    ])
                    ->required(),
                    
                Forms\Components\TextInput::make('payment_method')
                    ->label('Método de Pagamento')
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('payment_id')
                    ->label('ID do Pagamento')
                    ->maxLength(255),
                    
                Forms\Components\KeyValue::make('metadata')
                    ->label('Metadados')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BRL')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'refunded' => 'Reembolsado',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'refunded' => 'Reembolsado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
