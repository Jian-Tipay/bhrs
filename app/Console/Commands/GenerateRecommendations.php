<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CollaborativeFilteringService;
use Illuminate\Console\Command;

class GenerateRecommendations extends Command
{
    protected $signature = 'recommendations:generate {--user_id=}';
    protected $description = 'Generate recommendations for users';

    public function handle(CollaborativeFilteringService $cfService)
    {
        $userId = $this->option('user_id');

        if ($userId) {
            $users = User::where('id', $userId)->where('role', 'user')->get();
        } else {
            $users = User::where('role', 'user')->get();
        }

        $this->info("Generating recommendations for {$users->count()} users...");
        $bar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $cfService->clearUserCache($user->id);
            $cfService->getRecommendations($user->id, 10);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nRecommendations generated successfully!");
    }
}