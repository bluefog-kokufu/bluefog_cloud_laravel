<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Services\DemoDataService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingsService $settingsService,
        private readonly DemoDataService $demoDataService,
    ) {}

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

    /**
     * デモデータを初期化する。実データを消去する破壊的な操作のため本番環境では無効化する
     */
    public function resetDemo(): RedirectResponse
    {
        if (app()->environment('production')) {
            throw new NotFoundHttpException;
        }

        $this->demoDataService->reset();

        return redirect()->route('settings')->with('status', 'デモデータを初期化しました。');
    }
}
