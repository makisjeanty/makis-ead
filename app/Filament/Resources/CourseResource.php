<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Gestão Acadêmica';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes do Curso')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título do Curso')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        // --- AQUI ESTÁ A MÁGICA: O CAMPO DE CATEGORIA ---
                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                            ])
                            ->required(),
                        // ------------------------------------------------

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publicado no Site')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Configurações de Venda')
                    ->schema([
                        Forms\Components\Select::make('level')
                            ->label('Dificuldade')
                            ->options([
                                'Iniciante' => 'Iniciante',
                                'Intermediário' => 'Intermediário',
                                'Avançado' => 'Avançado',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('price')
                            ->label('Preço (0 para Grátis)')
                            ->numeric()
                            ->prefix('R$')
                            ->default(0.00),

                        Forms\Components\Textarea::make('description')
                            ->label('Descrição Curta')
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('image_url')
                            ->url()
                            ->label('URL da Imagem de Capa'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->circular()->label('Capa'),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('price')->money('BRL')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Ativo'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->relationship('category', 'name')->label('Filtrar por Categoria'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
