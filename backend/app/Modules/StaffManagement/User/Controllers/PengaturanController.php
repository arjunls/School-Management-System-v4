<?php

namespace App\Modules\StaffManagement\User\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Setting;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $profile = SchoolProfile::firstOrCreate(['id' => 1], ['name' => 'SMK Negeri 1']);
        $darkMode = Setting::getValue('dark_mode', 'false');
        $locale = Setting::getValue('locale', 'id');
        return view('pengaturan.index', compact('profile', 'darkMode', 'locale'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:20',
            'kepala_sekolah' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'dark_mode' => 'nullable|in:true,false',
            'locale' => 'nullable|in:id,en',
        ]);

        $profile = SchoolProfile::firstOrCreate(['id' => 1]);
        $profile->update([
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'npsn' => $data['npsn'] ?? null,
            'kepala_sekolah' => $data['kepala_sekolah'] ?? null,
            'akreditasi' => $data['akreditasi'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        Setting::setValue('dark_mode', $data['dark_mode'] ?? 'false');
        Setting::setValue('locale', $data['locale'] ?? 'id');

        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan berhasil disimpan');
    }
}
