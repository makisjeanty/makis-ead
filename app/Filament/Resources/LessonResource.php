<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup = 'Gestão Acadêmica';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('module_id')
                    ->relationship('module', 'title')
                    ->required()
                    ->label('Módulo'),
                
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Título da Aula'),

                // AQUI ESTÁ O EDITOR RICO PARA O CONTEÚDO
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull()
                    ->label('Conteúdo da Aula'),

                Forms\Components\TextInput::make('video_url')
                    ->url()
                    ->prefix('https://')
                    ->label('Link do Vídeo (YouTube/Vimeo)'),

                Forms\Components\FileUpload::make('pdf_file')
                    ->directory('lesson_pdfs')
                    ->label('Arquivo PDF de Apoio')
                    ->downloadable(),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('xp_reward')
                            ->numeric()
                            ->default(10)
                            ->label('XP de Recompensa'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->label('Ordem da Aula'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('module.course.title')->label('Curso')->sortable(),
                Tables\Columns\TextColumn::make('module.title')->label('Módulo'),
                Tables\Columns\TextColumn::make('title')->label('Aula')->searchable(),
                Tables\Columns\TextColumn::make('xp_reward')->label('XP'),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}
