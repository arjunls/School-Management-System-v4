<?php

namespace App\Modules\Security\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Security\Models\TwoFactorAuth;
use App\Modules\Security\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class SecurityWebController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();
        $securityLogs = SecurityLog::with('user')->orderBy('created_at', 'desc')->paginate(20);

        return view('security.index', compact('twoFactor', 'securityLogs'));
    }

    public function enable2fa()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $qrCodeUrl = $google2fa->getQRCodeGoogleUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('security.2fa-setup', compact('secret', 'qrCodeUrl'));
    }

    public function verify2fa(Request $request)
    {
        $request->validate(['code' => 'required|string|max:6']);
        $user = Auth::user();
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->firstOrNew();

        $google2fa = new Google2FA();

        if ($google2fa->verify($request->code, $twoFactor->secret)) {
            $twoFactor->update([
                'is_enabled' => true,
                'secret' => $twoFactor->secret,
                'backup_codes' => $this->generateBackupCodes(),
            ]);

            return redirect()->route('security.index')
                ->with('success', '2FA berhasil diaktifkan!');
        }

        return back()->with('error', 'Kode verifikasi salah.');
    }

    public function disable2fa()
    {
        $user = Auth::user();
        TwoFactorAuth::where('user_id', $user->id)->delete();

        return redirect()->route('security.index')
            ->with('success', '2FA berhasil dinonaktifkan.');
    }

    public function generateBackupCodes()
    {
        $user = Auth::user();
        $twoFactor = TwoFactorAuth::where('user_id', $user->id)->first();

        if ($twoFactor) {
            $codes = $this->generateBackupCodes();
            $twoFactor->update(['backup_codes' => $codes]);

            return redirect()->route('security.index')
                ->with('success', 'Kode cadangan baru berhasil dibuat.');
        }

        return redirect()->route('security.index')->with('error', '2FA belum diaktifkan.');
    }

    public function securityActivity()
    {
        $securityLogs = SecurityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('security.activity', compact('securityLogs'));
    }

    private function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        return $codes;
    }
}
