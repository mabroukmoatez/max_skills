<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chapitres; // <-- Import the Chapitres model
use App\Models\Lessons;   // <-- Import the Lessons model
use Illuminate\Support\Facades\DB; // <-- Import DB for transactions

class FixLessonOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lessons:fix-order'; // This is the command you will run

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Iterates through all chapters and corrects the order_num for each lesson sequentially, starting from 1.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting the lesson order correction process...');

        // Get all chapters that have at least one lesson.
        // This is more efficient than getting all chapters and then checking.
        $chaptersWithLessons = Chapitres::whereHas('lessons')->get();

        if ($chaptersWithLessons->isEmpty()) {
            $this->info('No chapters with lessons found. Nothing to do.');
            return 0;
        }

        $totalChaptersProcessed = 0;
        $totalLessonsUpdated = 0;

        // Create a progress bar for a better user experience
        $progressBar = $this->output->createProgressBar($chaptersWithLessons->count());
        $progressBar->start();

        foreach ($chaptersWithLessons as $chapter) {
            // Using a transaction ensures that if something fails for a chapter,
            // all its lesson updates are rolled back.
            DB::transaction(function () use ($chapter, &$totalLessonsUpdated) {
                // Get all lessons for the current chapter, ordered by their creation date or ID.
                // This provides a consistent, default order.
                $lessonsToOrder = $chapter->lessons()->orderBy('id', 'asc')->get();

                $orderCounter = 1;
                foreach ($lessonsToOrder as $lesson) {
                    // Update the order_num for each lesson
                    $lesson->order_num = $orderCounter;
                    $lesson->save();
                    $orderCounter++;
                    $totalLessonsUpdated++;
                }
            });

            $totalChaptersProcessed++;
            $progressBar->advance(); // Advance the progress bar
        }

        $progressBar->finish(); // Finish the progress bar

        $this->newLine(2); // Add some space for cleaner output
        $this->info("Process completed successfully!");
        $this->line("- Total Chapters Processed: <fg=yellow>{$totalChaptersProcessed}</>");
        $this->line("- Total Lessons Updated: <fg=yellow>{$totalLessonsUpdated}</>");

        return 0; // Successful command execution
    }
}
