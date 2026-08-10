<?php

namespace App\Http\Controllers;

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
                    'link' => $notice->link,
                    'pdf' => $notice->pdf_link,
                ];
            });

        return view('dashboard', array_merge(
            ['notices' => $notices],
            $this->dashboardService->summary(),
        ));
    }
}
