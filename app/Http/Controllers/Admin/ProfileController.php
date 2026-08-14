<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('admin.profile', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique(User::class)->ignore($user->id)],
        ], [
            'name.required' => '氏名を入力してください。',
            'email.required' => 'メールアドレス(ログインID)を入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'email.unique' => 'このメールアドレスは既に使用されています。',
        ]);

        $user->fill($validated);
        $user->save();

        return redirect()->route('profile')->with('profile_success', '更新しました。');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8'],
        ], [
            'current_password.required' => '現在のパスワードを入力してください。',
            'current_password.current_password' => '現在のパスワードが一致していません。',
            'new_password.required' => '新しいパスワードを入力してください。',
            'new_password.min' => '新しいパスワードは8文字以上で入力してください。',
        ]);

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return redirect()->route('profile')->with('password_success', 'パスワードを変更しました。');
    }
}
