<?php

namespace App\Http\Controllers;

use App\Models\AutomationHistory;
use App\Models\Video;
use App\Jobs\RunAutomationJob;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function index()
    {
        $histories = AutomationHistory::with('videos')
            ->latest()
            ->paginate(10);

        $totalJobs = AutomationHistory::count();
        $totalVideos = Video::count();

        return view('dashboard', compact(
            'histories',
            'totalJobs',
            'totalVideos'
        ));
    }

    public function start(Request $request)
    {
        $request->validate([
            'duration' => 'required|integer|min:5|max:300'
        ]);

        $history = AutomationHistory::create([
            'user_id' => null,
            'duration' => $request->duration,
            'status' => 'pending',
            'total_videos' => 0,
            'started_at' => now(),
        ]);

        RunAutomationJob::dispatch(
            $history->id,
            $request->duration
        );

        return redirect()
            ->route('dashboard')
            ->with('success', 'Automation berhasil dijalankan!');
    }
}