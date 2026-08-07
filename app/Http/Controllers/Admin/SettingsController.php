<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly SettingsService $settingsService) {}

    public function edit(): View
    {
        $company = $this->settingsService->get();

        return view('admin.settings.edit', compact('company'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $company = $this->settingsService->get();
        $this->settingsService->update($company, $request->validated());

        return redirect()->route('settings')->with('status', '保存しました。');
    }
}
