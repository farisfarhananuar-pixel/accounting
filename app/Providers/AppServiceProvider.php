<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Auto-create storage symlink if not exists
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');

        if (!file_exists($linkPath)) {
            symlink($targetPath, $linkPath);
        }
    }
}
