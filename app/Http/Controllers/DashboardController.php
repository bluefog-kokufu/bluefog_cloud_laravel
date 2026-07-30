<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
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

        return view('dashboard', compact('notices'));
    }
}
