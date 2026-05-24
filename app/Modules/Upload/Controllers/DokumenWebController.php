<?php

namespace App\Modules\Upload\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenWebController extends Controller
{
    public function index()
    {
        $documents = Document::with('user')->orderBy('created_at', 'desc')->paginate(25);
        $categories = Document::select('category')->distinct()->whereNotNull('category')->pluck('category');
        return view('dokumen.index', compact('documents', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $doc = Document::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'category' => $data['category'] ?? null,
        ]);

        activity()->performedOn($doc)->log('created');
        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diunggah');
    }

    public function download(Document $dokumen)
    {
        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            return redirect()->route('dokumen.index')->with('error', 'File tidak ditemukan');
        }
        return Storage::disk('public')->download($dokumen->file_path, $dokumen->title);
    }

    public function destroy(Document $dokumen)
    {
        Storage::disk('public')->delete($dokumen->file_path);
        $dokumen->delete();
        activity()->performedOn($dokumen)->log('deleted');
        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil dihapus');
    }
}
