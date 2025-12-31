<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card'; // Ícone de cartão
    protected static ?string $navigationGroup = 'Vendas & Financeiro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('Aluno'),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'title') // Caso tenha relacionamento direto no model Payment
                    ->label('Curso Comprado'),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('R$')
                    ->required(),
                Forms\Components\Select::make('provider')
                    ->options([
                        'stripe' => 'Stripe',
                        'mercadopago' => 'Mercado Pago',
                        'manual' => 'Manual / Pix',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pendente',
                        'paid' => 'Pago',
                        'failed' => 'Falhou',
                        'refunded' => 'Reembolsado',
                    ])
                    ->default('paid')
                    ->required(),
                Forms\Components\TextInput::make('transaction_id')
                    ->label('ID da Transação'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Aluno')->searchable(),
                Tables\Columns\TextColumn::make('course.title')->label('Curso')->searchable()->placeholder('Venda Direta'),
                Tables\Columns\TextColumn::make('amount')->money('BRL')->label('Valor')->sortable(),
                Tables\Columns\TextColumn::make('provider')->label('Método')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->label('Data'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'paid' => 'Pago',
                        'pending' => 'Pendente',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
