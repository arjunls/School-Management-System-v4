<?php namespace App\Modules\Ukk\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Ukk\Models\CertificationSchema;
use App\Modules\Ukk\Models\Certification;
use App\Models\User;
use Illuminate\Http\Request;

class UkkWebController extends Controller
{
    public function index()
    {
        $schemas = CertificationSchema::withCount('certifications')->orderBy('name')->get();
        $certifications = Certification::with('schema', 'student', 'assessor')
            ->orderBy('exam_date', 'desc')->paginate(25);
        return view('ukk.index', compact('schemas', 'certifications'));
    }

    public function createSchema()
    {
        return view('ukk.schema-form');
    }

    public function storeSchema(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|string|max:100',
        ]);
        CertificationSchema::create($d);
        return redirect()->route('ukk.index')->with('success', 'Skema sertifikasi tersimpan');
    }

    public function editSchema(CertificationSchema $schema)
    {
        return view('ukk.schema-form', compact('schema'));
    }

    public function updateSchema(Request $r, CertificationSchema $schema)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|string|max:100',
        ]);
        $schema->update($d);
        return redirect()->route('ukk.index')->with('success', 'Skema diperbarui');
    }

    public function destroySchema(CertificationSchema $schema)
    {
        $schema->certifications()->delete();
        $schema->delete();
        return redirect()->route('ukk.index')->with('success', 'Skema dihapus');
    }

    public function createCert()
    {
        $schemas = CertificationSchema::orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $assessors = User::whereIn('role', ['guru', 'admin', 'super-admin'])->orderBy('name')->get();
        return view('ukk.cert-form', compact('schemas', 'students', 'assessors'));
    }

    public function storeCert(Request $r)
    {
        $d = $r->validate([
            'schema_id' => 'required|exists:certification_schemas,id',
            'student_id' => 'required|exists:users,id',
            'assessor_id' => 'nullable|exists:users,id',
            'exam_date' => 'nullable|date',
            'result' => 'nullable|string|max:50',
            'certificate_number' => 'nullable|string|max:100|unique:certifications,certificate_number',
            'status' => 'nullable|in:registered,passed,failed',
        ]);
        Certification::create($d);
        return redirect()->route('ukk.index')->with('success', 'Sertifikasi tersimpan');
    }

    public function editCert(Certification $cert)
    {
        $schemas = CertificationSchema::orderBy('name')->get();
        $students = User::where('role', 'student')->orderBy('name')->get();
        $assessors = User::whereIn('role', ['guru', 'admin', 'super-admin'])->orderBy('name')->get();
        return view('ukk.cert-form', compact('cert', 'schemas', 'students', 'assessors'));
    }

    public function updateCert(Request $r, Certification $cert)
    {
        $d = $r->validate([
            'schema_id' => 'required|exists:certification_schemas,id',
            'student_id' => 'required|exists:users,id',
            'assessor_id' => 'nullable|exists:users,id',
            'exam_date' => 'nullable|date',
            'result' => 'nullable|string|max:50',
            'certificate_number' => 'nullable|string|max:100|unique:certifications,certificate_number,' . $cert->id,
            'status' => 'nullable|in:registered,passed,failed',
        ]);
        $cert->update($d);
        return redirect()->route('ukk.index')->with('success', 'Sertifikasi diperbarui');
    }

    public function destroyCert(Certification $cert)
    {
        $cert->delete();
        return redirect()->route('ukk.index')->with('success', 'Data sertifikasi dihapus');
    }
}
