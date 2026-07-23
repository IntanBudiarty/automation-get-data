<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationHistory;
use App\Models\Video;
use App\Jobs\RunAutomationJob;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'duration' => 'nullable|integer|min:5|max:300',
        ]);

        $duration = (int) ($request->duration ?? 30);
        $user = $request->user();

        $history = AutomationHistory::create([
            'user_id' => $user?->id,
            'duration' => $duration,
            'status' => 'pending',
            'total_videos' => 0,
            'started_at' => now(),
        ]);

        RunAutomationJob::dispatch($history->id, $duration);

        return response()->json([
            'success' => true,
            'message' => 'Job automation berhasil dimasukkan ke queue',
            'data' => [
                'history_id' => $history->id,
                'status' => $history->status,
                'duration' => $history->duration,
            ]
        ], 202);
    }

    public function callback(Request $request)
    {
        $request->validate([
            'history_id' => 'required|integer',
            'status' => 'required|string',
            'videos' => 'nullable|array',
        ]);

        $history = AutomationHistory::find($request->history_id);

        if (!$history) {
            return response()->json(['success' => false, 'message' => 'History ID tidak ditemukan'], 404);
        }

        $videos = $request->videos ?? [];
        $status = $request->status;

        $history->update([
            'status' => $status,
            'total_videos' => count($videos),
            'results' => $videos,
            'completed_at' => now(),
        ]);

        // Simpan setiap video ke tabel `videos`
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

        return response()->json([
            'success' => true,
            'message' => 'Hasil automation berhasil disimpan',
            'total_saved' => count($videos),
        ]);
    }
}