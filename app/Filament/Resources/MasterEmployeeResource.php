<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasterEmployeeResource\Pages;
use App\Filament\Resources\MasterEmployeeResource\RelationManagers;
use App\Models\MasterEmployee;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use stdClass;

class MasterEmployeeResource extends Resource
{
    protected static ?string $model = MasterEmployee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Master Management';
    protected static ?string $pluralLabel = 'Jadwal Pekerja';
    protected static ?string $label = 'Jadwal Pekerja';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            
                        Forms\Components\Select::make('user_id')
                            ->label('Pegawai')
                            ->relationship('user', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('work_as')
                            ->label('Posisi Kerja')
                            ->required(),
                            Forms\Components\CheckboxList::make('work_day')
                                ->label('Hari Kerja')
                                ->options([
                                    'Senin' => 'Senin',
                                    'Selasa' => 'Selasa',
                                    'Rabu' => 'Rabu',
                                    'Kamis' => 'Kamis',
                                    'Jumat' => 'Jumat',
                                    'Sabtu' => 'Sabtu',
                                    'Minggu' => 'Minggu',
                                ])
                                ->columns(4)
                                ->afterStateUpdated(function ($state) {
                                    // Mengubah array menjadi string yang dipisahkan koma
                                    return implode(',', $state);
                                }),
                                Grid::make(2)
                                    ->schema([
                                        
                                        Forms\Components\TimePicker::make('work_start')
                                        ->label('Mulai Kerja')
                                        ->required(),
                                        Forms\Components\TimePicker::make('work_end')
                                        ->label('Selesai Kerja')
                                        ->required(),
                                    ]),
                        Forms\Components\Textarea::make('notes')
                            ->label('Keterangan')
                            ->nullable(),
                        ]);
        

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                ),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\TextColumn::make('work_as')
                    ->label('Posisi Kerja'),
                Tables\Columns\TextColumn::make('work_day')
                    ->label('Hari Kerja'),
                Tables\Columns\TextColumn::make('work_start')
                    ->label('Mulai Kerja'),
                Tables\Columns\TextColumn::make('work_end')
                    ->label('Selesai Kerja'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Keterangan'),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListMasterEmployees::route('/'),
            'create' => Pages\CreateMasterEmployee::route('/create'),
            'edit' => Pages\EditMasterEmployee::route('/{record}/edit'),
        ];
    }
}
