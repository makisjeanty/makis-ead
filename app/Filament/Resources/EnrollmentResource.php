<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestão de Alunos';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Matrícula';

    protected static ?string $pluralLabel = 'Matrículas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Aluno')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                    
                Forms\Components\Select::make('course_id')
                    ->label('Curso')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->required(),
                    
                Forms\Components\DateTimePicker::make('enrolled_at')
                    ->label('Data de Matrícula')
                    ->default(now())
                    ->required(),
                    
                Forms\Components\TextInput::make('progress_percentage')
                    ->label('Progresso (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%'),
                    
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Data de Conclusão')
                    ->nullable(),
                    
                Forms\Components\DateTimePicker::make('certificate_issued_at')
                    ->label('Certificado Emitido em')
                    ->nullable(),
                    
                Forms\Components\TextInput::make('certificate_code')
                    ->label('Código do Certificado')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progresso')
                    ->suffix('%')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                    
                Tables\Columns\IconColumn::make('completed_at')
                    ->label('Concluído')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\IconColumn::make('certificate_issued_at')
                    ->label('Certificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('enrolled_at')
                    ->label('Matriculado em')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Curso')
                    ->relationship('course', 'title'),
                    
                Tables\Filters\Filter::make('completed')
                    ->label('Concluídos')
                    ->query(fn ($query) => $query->whereNotNull('completed_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('issue_certificate')
                    ->label('Emitir Certificado')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->visible(fn (Enrollment $record) => $record->isCompleted() && !$record->certificate_issued_at)
                    ->action(fn (Enrollment $record) => $record->issueCertificate())
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
