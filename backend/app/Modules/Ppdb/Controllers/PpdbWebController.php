<?php

namespace App\Modules\Ppdb\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Ppdb\Models\PpdbPeriod;
use App\Modules\Ppdb\Models\PpdbApplicant;
use Illuminate\Http\Request;

class PpdbWebController extends Controller
{
    public function index()
    {
        $periods = PpdbPeriod::open()->get();
        return view('ppdb.index', compact('periods'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:ppdb_periods,id',
            'full_name' => 'required|string|max:255',
            'nisn' => 'required|string|max:20',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'religion' => 'required|string|max:50',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'previous_school' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
        ]);

        $period = PpdbPeriod::findOrFail($validated['period_id']);

        if (!$period->is_open) {
            return back()->with('error', 'Pendaftaran untuk periode ini sudah ditutup.');
        }

        if ($period->applicant_count >= $period->quota) {
            return back()->with('error', 'Kuota pendaftaran untuk periode ini sudah penuh.');
        }

        $year = date('Y');
        $lastReg = PpdbApplicant::whereYear('created_at', $year)
            ->where('period_id', $period->id)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastReg ? (int) substr($lastReg->registration_number, -5) + 1 : 1;
        $validated['registration_number'] = sprintf('PPDB/%s/%05d', $year, $sequence);
        $validated['status'] = 'registered';

        $applicant = PpdbApplicant::create($validated);

        return view('ppdb.register', compact('applicant'));
    }

    public function adminIndex()
    {
        $periods = PpdbPeriod::withCount('applicants')->orderBy('created_at', 'desc')->get();
        $applicants = PpdbApplicant::with('period')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $statuses = ['registered', 'verified', 'accepted', 'rejected'];

        return view('ppdb.admin.applicants.index', compact('periods', 'applicants', 'statuses'));
    }

    public function adminPeriods()
    {
        $periods = PpdbPeriod::withCount('applicants')->orderBy('created_at', 'desc')->get();
        return view('ppdb.admin.periods.index', compact('periods'));
    }

    public function adminPeriodsCreate()
    {
        return view('ppdb.admin.periods.form');
    }

    public function adminPeriodsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,closed',
        ]);

        PpdbPeriod::create($validated);

        return redirect()->route('ppdb.admin.periods')
            ->with('success', 'Periode PPDB berhasil ditambahkan.');
    }

    public function adminPeriodsEdit(PpdbPeriod $period)
    {
        return view('ppdb.admin.periods.form', compact('period'));
    }

    public function adminPeriodsUpdate(Request $request, PpdbPeriod $period)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_year' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,closed',
        ]);

        $period->update($validated);

        return redirect()->route('ppdb.admin.periods')
            ->with('success', 'Periode PPDB berhasil diperbarui.');
    }

    public function adminPeriodsDestroy(PpdbPeriod $period)
    {
        if ($period->applicants()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sudah memiliki pendaftar.');
        }

        $period->delete();

        return redirect()->route('ppdb.admin.periods')
            ->with('success', 'Periode PPDB berhasil dihapus.');
    }

    public function adminApplicants(Request $request)
    {
        $query = PpdbApplicant::with('period');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period_id')) {
            $query->where('period_id', $request->period_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $applicants = $query->orderBy('created_at', 'desc')->paginate(25);
        $periods = PpdbPeriod::orderBy('created_at', 'desc')->get();
        $statuses = ['registered', 'verified', 'accepted', 'rejected'];

        return view('ppdb.admin.applicants.index', compact('applicants', 'periods', 'statuses'));
    }

    public function adminApplicantsShow(PpdbApplicant $applicant)
    {
        $applicant->load('period');
        return view('ppdb.admin.applicants.show', compact('applicant'));
    }

    public function adminApplicantsUpdateStatus(Request $request, PpdbApplicant $applicant)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,verified,accepted,rejected',
        ]);

        $applicant->update($validated);

        return redirect()->route('ppdb.admin.applicants.show', $applicant)
            ->with('success', 'Status pendaftar berhasil diperbarui.');
    }
}
