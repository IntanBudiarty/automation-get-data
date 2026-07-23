<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = AutomationHistory::with('videos');

        if ($user) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('videos', function ($vq) use ($search) {
                    $vq->where('title', 'like', "%{$search}%")
                       ->orWhere('channel', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $histories = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $histories->items(),
            'meta' => [
                'current_page' => $histories->currentPage(),
                'last_page' => $histories->lastPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
            ]
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $query = AutomationHistory::with('videos')->where('id', $id);

        if ($user) {
            $query->where('user_id', $user->id);
        }

        $history = $query->first();

        if (!$history) {
            return response()->json([
                'success' => false,
                'message' => 'Riwayat history tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
