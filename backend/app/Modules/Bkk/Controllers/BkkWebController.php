<?php namespace App\Modules\Bkk\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Bkk\Models\Company;
use App\Modules\Bkk\Models\JobVacancy;
use App\Modules\Bkk\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;

class BkkWebController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('vacancies')->orderBy('name')->paginate(10);
        $vacancies = JobVacancy::with('company')->orderBy('created_at', 'desc')->paginate(10);
        return view('bkk.index', compact('companies', 'vacancies'));
    }

    public function createCompany()
    {
        return view('bkk.company-form');
    }

    public function storeCompany(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'mou_date' => 'nullable|date',
            'mou_expiry' => 'nullable|date|after_or_equal:mou_date',
            'status' => 'nullable|in:active,inactive',
        ]);
        Company::create($d);
        return redirect()->route('bkk.index')->with('success', 'Perusahaan tersimpan');
    }

    public function editCompany(Company $company)
    {
        return view('bkk.company-form', compact('company'));
    }

    public function updateCompany(Request $r, Company $company)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'nullable|string|max:255',
            'mou_date' => 'nullable|date',
            'mou_expiry' => 'nullable|date|after_or_equal:mou_date',
            'status' => 'nullable|in:active,inactive',
        ]);
        $company->update($d);
        return redirect()->route('bkk.index')->with('success', 'Perusahaan diperbarui');
    }

    public function destroyCompany(Company $company)
    {
        $company->vacancies()->delete();
        $company->delete();
        return redirect()->route('bkk.index')->with('success', 'Perusahaan dihapus');
    }

    public function createVacancy()
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return view('bkk.vacancy-form', compact('companies'));
    }

    public function storeVacancy(Request $r)
    {
        $d = $r->validate([
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'slots' => 'nullable|integer|min:1',
            'closing_date' => 'nullable|date',
            'status' => 'nullable|in:open,closed',
        ]);
        JobVacancy::create($d);
        return redirect()->route('bkk.index')->with('success', 'Lowongan tersimpan');
    }

    public function editVacancy(JobVacancy $vacancy)
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return view('bkk.vacancy-form', compact('vacancy', 'companies'));
    }

    public function updateVacancy(Request $r, JobVacancy $vacancy)
    {
        $d = $r->validate([
            'company_id' => 'required|exists:companies,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'slots' => 'nullable|integer|min:1',
            'closing_date' => 'nullable|date',
            'status' => 'nullable|in:open,closed',
        ]);
        $vacancy->update($d);
        return redirect()->route('bkk.index')->with('success', 'Lowongan diperbarui');
    }

    public function destroyVacancy(JobVacancy $vacancy)
    {
        $vacancy->applications()->delete();
        $vacancy->delete();
        return redirect()->route('bkk.index')->with('success', 'Lowongan dihapus');
    }

    public function applications(JobVacancy $vacancy)
    {
        $vacancy->load('applications.student', 'company');
        return view('bkk.applications', compact('vacancy'));
    }

    public function apply(Request $r, JobVacancy $vacancy)
    {
        $r->validate(['student_id' => 'required|exists:users,id']);
        $exists = JobApplication::where('vacancy_id', $vacancy->id)
            ->where('student_id', $r->student_id)->exists();
        if ($exists) {
            return back()->with('error', 'Siswa sudah melamar lowongan ini');
        }
        JobApplication::create([
            'vacancy_id' => $vacancy->id,
            'student_id' => $r->student_id,
            'status' => 'applied',
        ]);
        return redirect()->route('bkk.applications', $vacancy)->with('success', 'Lamaran tersimpan');
    }

    public function updateApplicationStatus(Request $r, JobApplication $application)
    {
        $d = $r->validate([
            'status' => 'required|in:applied,reviewed,interview,accepted,rejected',
            'notes' => 'nullable|string',
        ]);
        $application->update($d);
        return back()->with('success', 'Status lamaran diperbarui');
    }
}
