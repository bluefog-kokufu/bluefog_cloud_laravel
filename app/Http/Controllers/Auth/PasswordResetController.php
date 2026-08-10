<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(private readonly PasswordResetService $passwordResetService) {}

    public function showForgotForm(): View
    {
        return view('auth.password-forgot');
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        $this->passwordResetService->sendResetLink($request->validated('email'));

        // 登録有無に関わらず同じメッセージを返し、メールアドレスの存在を推測されないようにする
        return redirect()->route('password.forgot')
            ->with('status', '入力されたメールアドレス宛にパスワード再設定用のメールを送信しました。');
    }

    public function showResetForm(Request $request): View
    {
        $email = (string) $request->query('email');
        $token = (string) $request->query('token');

        $state = ($email !== '' && $token !== '')
            ? $this->passwordResetService->checkToken($email, $token)
            : 'invalid';

        if ($state !== 'valid') {
            return view('auth.password-timeout', ['expired' => $state === 'expired']);
        }

        return view('auth.password-reset', ['email' => $email, 'token' => $token]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse|View
    {
        $validated = $request->validated();

        $success = $this->passwordResetService->resetPassword(
            $validated['email'],
            $validated['token'],
            $validated['password'],
        );

        if (! $success) {
            $state = $this->passwordResetService->checkToken($validated['email'], $validated['token']);

            return view('auth.password-timeout', ['expired' => $state === 'expired']);
        }

        return redirect()->route('login')->with('status', 'パスワードを再設定しました。新しいパスワードでログインしてください。');
    }
}
