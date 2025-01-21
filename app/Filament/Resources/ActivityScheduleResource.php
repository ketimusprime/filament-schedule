<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityScheduleResource\Pages;
use App\Filament\Resources\ActivityScheduleResource\RelationManagers;
use App\Models\ActivitySchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityScheduleResource extends Resource
{
    protected static ?string $model = ActivitySchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $pluralLabel = 'Penjadwalan';
    protected static ?string $label = 'Penjadwalan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Tabs::make('activity_schedules')
            ->tabs([
                Forms\Components\Tabs\Tab::make('Input Jadwal')
                ->schema([
                    Forms\Components\DatePicker::make('activity_date')
                    ->label('Tangga Aktifitas')
                    ->required(),
                Forms\Components\TimePicker::make('activity_time')
                    ->label('Waktu Aktifitas')
                    ->required(),
                Forms\Components\TextInput::make('No_OP')
                    ->label('Number Order'),
                Forms\Components\Select::make('order')
                    ->label('Tipe Order')
                    ->options([
                        'kerjasama' => 'Kerjasama',
                        'walkin' => 'Walk-in',
                        'online' => 'Online',
                        'reserve' => 'Reserve',
                ])
                ->required(),
                Forms\Components\TextInput::make('customer_name')
                    ->label('Nama Customer')
                    ->required(),
                Forms\Components\TextInput::make('customer_phone')
                    ->label('Hp Customer')
                    ->tel()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('subcategory_id')
                    ->label('Sub Kategori')
                    ->relationship('subcategory', 'name') 
                    ->reactive()
                    ->required(),
                Forms\Components\TextInput::make('package')
                    ->label('Nama Paket'),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan')
                    ->nullable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'done' => 'Done',
                        'cancel' => 'Cancel',
                        'confirmed' => 'Confirmed',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                    ]),
                Forms\Components\Tabs\Tab::make('Tambahkan Pekerja')
                ->schema([
                    Forms\Components\Repeater::make('employees')
                    ->label('Pekerja')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                        ->label('Pekerja')
                        ->relationship('user', 'name')
                        ->required(),
                        Forms\Components\TextInput::make('crew')
                        ->label('Sebagai')
                        ->required(),
                        Forms\Components\Textarea::make('notes')
                        ->label('Keterangan')
                        ->nullable(),
                        ])
            ]),
    ])->columnSpan('2')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('activity_date')->label('Tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('activity_time')->label('Waktu')->time()->sortable(),
                Tables\Columns\TextColumn::make('No_OP')->label('Number Order')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('order')->label('Tipe Order')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Nama Customer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')->label('Hp Customer')->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable(),
                Tables\Columns\TextColumn::make('subcategory.name')->label('Sub Kategori')->sortable(),
                Tables\Columns\TextColumn::make('package')->label('Nama Paket')->sortable(),
                Tables\Columns\TextColumn::make('description')->label('Keterangan')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Di buat oleh')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'primary' => fn ($state): bool => $state === 'pending',
                    'success' => fn ($state): bool => $state === 'done',
                    'danger' => fn ($state): bool => $state === 'cancel',
                    'warning' => fn ($state): bool => $state === 'confirmed',
                ]),
                Tables\Columns\TextColumn::make('employees')
                ->label('Pekerja')
                ->getStateUsing(fn ($record) => $record->employees->pluck('name')->join(', '))
                ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListActivitySchedules::route('/'),
            'create' => Pages\CreateActivitySchedule::route('/create'),
            'edit' => Pages\EditActivitySchedule::route('/{record}/edit'),
        ];
    }

    public static function afterSave(Form $form, $record)
    {
        // Ambil data dari form pekerja
        $employees = $form->getState()['employees'] ?? [];

        dd($employees);

        // Sinkronkan pekerja dengan jadwal aktivitas (menggunakan tabel pivot)
        if ($employees) {
            $record->employees()->sync(
                collect($employees)->mapWithKeys(function ($item) {
                    return [
                        $item['user_id'] => [
                            'crew' => $item['crew'],
                            'notes' => $item['notes'],
                        ]
                    ];
                })->toArray()
            );
        }
    }
}

