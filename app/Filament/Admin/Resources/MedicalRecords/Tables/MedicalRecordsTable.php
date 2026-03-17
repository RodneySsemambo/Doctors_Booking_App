<?php

namespace App\Filament\Admin\Resources\MedicalRecords\Tables;

use App\Models\MedicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MedicalRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.first_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('appointment.appointment_type')
                    ->label('Appointment Type')
                    ->sortable(),
                TextColumn::make('record_type'),
                TextColumn::make('title')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('file_path')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('file_type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('recorded_by')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('recorded_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('patient')
                    ->relationship('patient', 'first_name')
                    ->label('Patient')
                    ->searchable(),
                SelectFilter::make('doctor')
                    ->relationship('doctor', 'first_name')
                    ->label('Doctor')
                    ->searchable(),
                SelectFilter::make('record_date')
                    ->label('Recorded Date')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until')
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($query, $date) => $query->whereDate('recorded_date', '>=', $date))
                            ->when($data['until'], fn($query, $date) => $query->whereDate('recorded_date', '<=', $date));
                    }),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('download pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function (MedicalRecord $record) {
                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadView('medical-records.pdf', ['record' => $record])
                                ->output();
                        }, "medical-record-{$record->id}.pdf");
                    }),
                Action::make('share')
                    ->label('Share Record')
                    ->icon('heroicon-o-share')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (MedicalRecord $record) {
                        Notification::make()
                            ->success()
                            ->title('Record Shared')
                            ->body("The medical record '{$record->title}' has been shared successfully.")
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
