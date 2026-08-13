<?php

namespace App\Http\Controllers;

use App\Http\Requests\Internal\BootstrapRequest;
use App\Models\User;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InternalBootstrapController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    /**
     * admin側のプロビジョニング処理から呼び出され、テナントの初回ユーザーを作成し、
     * パスワード初期設定メール(既存の「パスワードを忘れた方」フローを流用)を送信する。
     * 再実行されても既存ユーザーのパスワードは上書きしない(冪等)
     */
    public function store(BootstrapRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->firstOrNew(['email' => $data['email']]);
        $isNewUser = ! $user->exists;

        $user->name = $data['name'];
        if ($isNewUser) {
            $user->password = Hash::make(Str::random(40));
        }
        $user->save();

        $this->passwordResetService->sendResetLink($data['email']);

        return response()->json(['status' => 'ok']);
    }
}
