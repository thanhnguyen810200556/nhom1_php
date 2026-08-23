<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed'        => 'Mật khẩu xác nhận không trùng khớp.',
            'password.min'              => 'Mật khẩu mới phải có tối thiểu :min ký tự.',
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không chính xác.'],
            ]);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Mật khẩu đã được cập nhật thành công.',
            ]);
        }

        return back()->with('status', 'password-updated');
    }
}