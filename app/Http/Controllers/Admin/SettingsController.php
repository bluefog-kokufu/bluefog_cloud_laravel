<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Services\DemoDataService;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
     * 他画面(請求書作成等)からモーダルで入金口座情報のみを素早く編集するためのフォームを返す
     */
    public function bankModal(): string
    {
        $company = $this->settingsService->get();

        return view('admin.settings.modal_bank', compact('company'))->render();
    }

    /**
     * モーダルから入金口座情報のみを更新する(他画面の入力内容を失わないようリダイレクトせずJSONで返す)
     */
    public function updateBank(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bank' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $company = $this->settingsService->get();
        $company = $this->settingsService->update($company, $validator->validated());

        return response()->json(['bank' => $company->bank]);
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
