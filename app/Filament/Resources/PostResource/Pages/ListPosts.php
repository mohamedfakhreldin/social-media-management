<?php

namespace App\Filament\Resources\PostResource\Pages;


use Filament\Actions\CreateAction;
use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Post')
                ->icon('heroicon-o-plus'),
        ];
    }
} 