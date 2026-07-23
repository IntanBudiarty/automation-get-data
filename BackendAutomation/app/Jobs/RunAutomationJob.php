<?php

namespace App\Jobs;

use App\Models\AutomationHistory;
use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RunAutomationJob implements ShouldQueue
{
    use Queueable;

    public int $historyId;
    public int $duration;

    public function __construct(int $historyId, int $duration = 30)
    {
        $this->historyId = $historyId;
        $this->duration = $duration;
    }

    public function handle(): void
    {
        $history = AutomationHistory::find($this->historyId);

        if (!$history) {
            Log::error("RunAutomationJob: History record with ID {$this->historyId} not found.");
            return;
        }

        $history->update([
            'status' => 'running',
            'started_at' => now(),
        ]);

        $scriptPath = base_path('../Automation/main.py');
        if (!file_exists($scriptPath)) {
            $scriptPath = base_path('Automation/main.py');
        }
        $apiUrl = config('app.url', 'http://127.0.0.1:8000') . '/api/automation/callback';

        $cmd = sprintf(
            'python "%s" --duration=%d --history_id=%d --api_url="%s"',
            $scriptPath,
            $this->duration,
            $this->historyId,
            $apiUrl
        );

        Log::info("Executing Playwright Python Command: " . $cmd);

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        // Extract JSON results from stdout output markers
        $videos = [];
        $capturing = false;
        $jsonLines = [];

        foreach ($output as $line) {
            if (trim($line) === '=== JSON_OUTPUT_START ===') {
                $capturing = true;
                continue;
            }
            if (trim($line) === '=== JSON_OUTPUT_END ===') {
                $capturing = false;
                break;
            }
            if ($capturing) {
                $jsonLines[] = $line;
            }
        }

        if (!empty($jsonLines)) {
            $jsonRaw = implode("\n", $jsonLines);
            $videos = json_decode($jsonRaw, true) ?? [];
        }

        $status = ($returnCode === 0 && !empty($videos)) ? 'completed' : ($returnCode === 0 ? 'completed' : 'failed');

        $history->update([
            'status' => $status,
            'total_videos' => count($videos),
            'results' => $videos,
            'completed_at' => now(),
        ]);

        // Simpan setiap video ke database
        foreach ($videos as $item) {
            Video::create([
                'automation_history_id' => $history->id,
                'title' => $item['title'] ?? 'Tanpa Judul',
                'channel' => $item['channel'] ?? '-',
                'url' => $item['url'] ?? '-',
                'views' => $item['views'] ?? null,
                'scraped_at' => $item['scraped_at'] ?? now(),
            ]);
        }

        Log::info("RunAutomationJob completed for History ID {$this->historyId}. Total videos: " . count($videos));
    }
}