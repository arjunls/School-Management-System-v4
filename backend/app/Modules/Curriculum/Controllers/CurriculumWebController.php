<?php

namespace App\Modules\Curriculum\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Academic\Subject\Models\Subject;
use App\Modules\Curriculum\Models\Atp;
use App\Modules\Curriculum\Models\Cp;
use App\Modules\Curriculum\Models\Tp;
use Illuminate\Http\Request;

class CurriculumWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Cp::with('subject');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('phase')) {
            $query->where('phase', $request->phase);
        }

        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }

        $cps = $query->orderBy('subject_id')->orderBy('code')->paginate(25);
        $subjects = Subject::orderBy('name')->get();

        return view('curriculum.index', compact('cps', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();

        return view('curriculum.cp-form', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'code' => 'required|string|max:50',
            'description' => 'required|string',
            'phase' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
        ]);

        Cp::create($data);

        return redirect()->route('curriculum.index')
            ->with('success', 'CP berhasil ditambahkan');
    }

    public function show(Cp $cp)
    {
        $cp->load('subject', 'tps.atps');

        return view('curriculum.cp', compact('cp'));
    }

    public function edit(Cp $cp)
    {
        $subjects = Subject::orderBy('name')->get();

        return view('curriculum.cp-form', compact('cp', 'subjects'));
    }

    public function update(Request $request, Cp $cp)
    {
        $data = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'code' => 'required|string|max:50',
            'description' => 'required|string',
            'phase' => 'nullable|string|max:20',
            'class' => 'nullable|string|max:50',
        ]);

        $cp->update($data);

        return redirect()->route('curriculum.index')
            ->with('success', 'CP berhasil diperbarui');
    }

    public function destroy(Cp $cp)
    {
        $cp->delete();

        return redirect()->route('curriculum.index')
            ->with('success', 'CP berhasil dihapus');
    }

    public function storeTp(Request $request, Cp $cp)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'description' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $data['cp_id'] = $cp->id;
        $data['order'] ??= $cp->tps()->count() + 1;

        Tp::create($data);

        return redirect()->route('curriculum.show', $cp)
            ->with('success', 'TP berhasil ditambahkan');
    }

    public function updateTp(Request $request, Cp $cp, Tp $tp)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50',
            'description' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $tp->update($data);

        return redirect()->route('curriculum.show', $cp)
            ->with('success', 'TP berhasil diperbarui');
    }

    public function destroyTp(Cp $cp, Tp $tp)
    {
        $tp->delete();

        return redirect()->route('curriculum.show', $cp)
            ->with('success', 'TP berhasil dihapus');
    }

    public function storeAtp(Request $request, Cp $cp, Tp $tp)
    {
        $data = $request->validate([
            'objective' => 'required|string',
            'material' => 'nullable|string',
            'assessment' => 'nullable|string',
            'method' => 'nullable|string',
            'hours' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
        ]);

        $data['tp_id'] = $tp->id;
        $data['order'] ??= $tp->atps()->count() + 1;

        Atp::create($data);

        return redirect()->route('curriculum.show', $cp)
            ->with('success', 'ATP berhasil ditambahkan');
    }

    public function updateAtp(Request $request, Cp $cp, Tp $tp, Atp $atp)
    {
        $data = $request->validate([
            'objective' => 'required|string',
            'material' => 'nullable|string',
            'assessment' => 'nullable|string',
            'method' => 'nullable|string',
            'hours' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
        ]);

        $atp->update($data);

        return redirect()->route('curriculum.show', $cp)
            ->with('success', 'ATP berhasil diperbarui');
    }

    public function destroyAtp(Cp $cp, Tp $tp, Atp $atp)
    {
        $atp->delete();

        return redirect()->route('curriculum.show', $cp)
            ->with('success', 'ATP berhasil dihapus');
    }
}
