<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityScheduleResource\Pages;
use App\Filament\Resources\ActivityScheduleResource\RelationManagers;
use App\Models\ActivitySchedule;
use Filament\Forms;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use stdClass;
use App\Models\MasterEmployee;



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
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Jadwal Aktifitas')
                        ->schema([
                            Forms\Components\DatePicker::make('activity_date')
                            ->label('Tangga Aktifitas')
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Menangani perubahan pada activity_date untuk mengambil data pegawai dan hari kerja
                                $employee = \App\Models\MasterEmployee::where('user_id', auth()->user()->id)->first();  // Ambil data pegawai berdasarkan user yang login
                                
                                if ($employee) {
                                    // Setelah memilih tanggal, otomatis mengisi nama pegawai, posisi kerja, dan hari kerja
                                    $set('user_id', auth()->user()->id); // Mengisi user_id
                                    $set('work_as', $employee->work_as); // Mengisi posisi kerja
                                    $workDays = is_array($employee->work_day) ? $employee->work_day : explode(',', $employee->work_day);
                                    $set('work_day', $workDays);
                                    $set('work_start', $employee->work_start); // Mengisi jam mulai kerja
                                    $set('work_end', $employee->work_end); // Mengisi jam selesai kerja
                                }
                            }),
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
                            ->searchable()
                            ->preload()
                            ->live() // Menambahkan event live untuk menangani perubahan
                            ->required()
                            ->reactive() // Membuat kategori ini bisa mengubah state di subkategori
                            ->afterStateUpdated(fn ($state, $set) => $set('subcategory_id', null)), // Reset subkategori ketika kategori diubah
                            Forms\Components\Select::make('subcategory_id')
                            ->label('Sub Kategori')
                            ->relationship('subcategory', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->reactive() // Membuat subkategori ini tergantung pada kategori yang dipilih
                            ->options(function ($get) {
                                $categoryId = $get('category_id'); // Mengambil ID kategori yang dipilih
                                return \App\Models\Subcategory::where('category_id', $categoryId)->pluck('name', 'id')->toArray(); // Mendapatkan subkategori berdasarkan kategori
                            }),
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
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(function () {
                                return auth()->user()->id; // Mengambil ID pengguna yang sedang login
                            }),
                        ]),
                         Forms\Components\Wizard\Step::make('Input Pekerja')
                         ->schema([
                            Forms\Components\Select::make('user_id')
                            ->label('Pegawai')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(function ($get) {
                                return $get('user_id'); // Mengambil user_id yang dipilih
                            })
                            ->reactive()  // Menambahkan reactive agar data diupdate
                            ->afterStateUpdated(function ($state, $set) {
                                // Ketika user_id dipilih, kita ambil data dari MasterEmployee
                                $employee = \App\Models\MasterEmployee::where('user_id', $state)->first();
                                if ($employee) {
                                    // Mengatur field terkait lainnya berdasarkan data dari MasterEmployee
                                    $set('work_as', $employee->work_as); // Mengisi posisi kerja
                                    $set('work_day', is_array($employee->work_day) ? $employee->work_day : explode(',', $employee->work_day)); // Mengisi hari kerja
                                    $set('work_start', $employee->work_start); // Mengisi jam mulai kerja
                                    $set('work_end', $employee->work_end); // Mengisi jam selesai kerja
                                } else {
                                    // Reset jika tidak ditemukan
                                    $set('work_as', '');
                                    $set('work_day', []);
                                    $set('work_start', '');
                                    $set('work_end', '');
                                }
                            }),

                        Forms\Components\TextInput::make('work_as')
                            ->label('Posisi Kerja')
                            ->default(function ($get) {
                                // Ambil posisi kerja berdasarkan user_id yang dipilih
                                $userId = $get('user_id');
                                return \App\Models\MasterEmployee::where('user_id', $userId)->first()->work_as ?? '';
                            }),

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
                            ->default(function ($get) {
                                $userId = $get('user_id');
                                // Ambil hari kerja berdasarkan user_id yang dipilih
                                $employee = \App\Models\MasterEmployee::where('user_id', $userId)->first();
                                return $employee ? (is_array($employee->work_day) ? $employee->work_day : explode(',', $employee->work_day)) : [];
                            })
                            ->afterStateUpdated(function ($state) {
                                return implode(',', $state); // Jika perlu, ubah array menjadi string
                            }),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('work_start')
                                    ->label('Mulai Kerja')
                                    ->default(function ($get) {
                                        $userId = $get('user_id');
                                        $employee = \App\Models\MasterEmployee::where('user_id', $userId)->first();
                                        return $employee->work_start ?? '';
                                    }),

                                Forms\Components\TimePicker::make('work_end')
                                    ->label('Selesai Kerja')
                                    ->default(function ($get) {
                                        $userId = $get('user_id');
                                        $employee = \App\Models\MasterEmployee::where('user_id', $userId)->first();
                                        return $employee->work_end ?? '';
                                    }),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label('Keterangan')
                            ->nullable(),
                    ])
    
                    ])
                    ->columnSpanFull()
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
        ])
            ->filters([
                SelectFilter::make('category_id')
                ->relationship('category', 'name')
                ->label('Kategori'),
                SelectFilter::make('subcategory_id')
                ->relationship('subcategory', 'name')
                ->label('Sub Kategori'),
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
            'index' => Pages\ListActivitySchedules::route('/'),
            'create' => Pages\CreateActivitySchedule::route('/create'),
            'edit' => Pages\EditActivitySchedule::route('/{record}/edit'),
        ];
    }

    
}

