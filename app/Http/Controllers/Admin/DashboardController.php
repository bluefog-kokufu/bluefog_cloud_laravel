<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(): View
    {
        $notices = Notice::query()
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->get()
            ->map(function (Notice $notice) {
                return [
                    'date' => $notice->published_at->format('Y.m.d'),
                    'title' => $notice->title,
                    'message' => $notice->content,
                ];
            });

        return view('admin.dashboard', array_merge(
            ['notices' => $notices],
            $this->dashboardService->summary(),
        ));
    }
}
