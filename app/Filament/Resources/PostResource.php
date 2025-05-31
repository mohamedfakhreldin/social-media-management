<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PostResource\Pages;
use App\Rules\ContentCheckCharatersLengthRule;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static array $middleware = [
        \App\Http\Middleware\PostRateLimit::class, // Example: Spatie permissions middleware
    ];
    protected static ?string $navigationGroup = 'Content Management';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create Post')
                ->icon('heroicon-o-plus'),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                  ,

                Forms\Components\Textarea::make('content')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => 
                        $set('character_count', strlen($state ?? '')))->rules(function(callable $get){
                            return [new ContentCheckCharatersLengthRule($get('platforms'))];
                        }),

                Forms\Components\TextInput::make('character_count')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull()
                    ->label('Character Count'),
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->user()->id)
            
                    ,

                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->directory('posts')
                    ->visibility('public'),

                Forms\Components\Select::make('platforms')
                    ->multiple()
                    ->relationship(
                        'platforms',
                        'name',
                        fn (Builder $query) => $query->whereHas('userActivePlatforms', function ($query) {
                            $query->where('user_id', auth()->id())
                                ->where('is_active', true);
                        })
                    )
                    ->required()
                    ->preload(),

                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ])
                    ->afterStateUpdated(function ( $set, $state) {
                     
                            $set('scheduled_time',now() );
                        
                    })
                    ->required()
                    ->reactive()
                    ->default('draft'),
                    
                Forms\Components\DateTimePicker::make('scheduled_time')
                ->required()
                ->afterOrEqual('today')->required()
                ->columnSpanFull()
                ->visible(fn ( $get) => $get('status') === 'scheduled')
                ->default(now()),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'published' => 'success',
                    }),

                Tables\Columns\TextColumn::make('scheduled_time')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('platforms.name')
                    ->badge(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                    ]),
                Tables\Filters\Filter::make('scheduled_time')
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_from'),
                        Forms\Components\DatePicker::make('scheduled_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_time', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_time', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])
              
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
} 