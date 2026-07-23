<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
class VideoController extends Controller
{
    public function index() {
        $videos = Video::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $videos
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'channel' => 'nullable',
            'url' => 'required',
            'views' => 'nullable',
            'scraped_at' => 'nullable',
        ]);

        $video = Video::create([
            'automation_history_id' => $request->automation_history_id ?? null,
            'title' => $request->title,
            'channel' => $request->channel ?? '-',
            'url' => $request->url,
            'views' => $request->views,
            'scraped_at' => $request->scraped_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data video berhasil disimpan',
            'data' => $video
        ], 201);
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'videos' => 'required|array',
        ]);

        $savedVideos = [];
        foreach ($request->videos as $item) {
            if (empty($item['title']) || empty($item['url'])) {
                continue;
            }

            $savedVideos[] = Video::create([
                'automation_history_id' => $item['automation_history_id'] ?? null,
                'title' => $item['title'],
                'channel' => $item['channel'] ?? '-',
                'url' => $item['url'],
                'views' => $item['views'] ?? null,
                'scraped_at' => $item['scraped_at'] ?? now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($savedVideos) . ' data video berhasil disimpan ke database',
            'total_saved' => count($savedVideos),
            'data' => $savedVideos
        ], 201);
    }
}
