<?php

namespace App\Http\Controllers;

use App\Models\QR_Code;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrGenerator;
use Illuminate\Support\Facades\Auth;

class QR_CodeController extends Controller
{
    /**
     * Display list of QR codes
     */
    public function index()
    {
        $qrCodes = QR_Code::latest()->paginate(10);
        return view('reference.qr_code.index', compact('qrCodes'));
    }

    /**
     * Generate new QR Code
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|unique:qr_codes,code',
        ]);

        $code = $request->code ?? 'LCC-' . strtoupper(Str::random(8));

        QR_Code::create([
            'code' => $code,
            'status' => QR_Code::STATUS_ACTIVE,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('qr_codes.index')
            ->with('success', 'QR Code generated successfully.');
    }

    /**
     * Show QR Image
     */
    public function show($id)
    {
        $qr = QR_Code::findOrFail($id);

        return response(
            QrGenerator::size(300)->generate($qr->code)
        )->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Mark as Used
     */
    public function markUsed($id)
    {
        $qr = QR_Code::findOrFail($id);

        if ($qr->isUsed()) {
            return back()->with('error', 'QR Code is already used.');
        }

        $qr->markAsUsed();

        return redirect()->route('qr_codes.index')
            ->with('success', 'QR marked as used.');
    }

    public function destroy($id)
{
    $qr = QR_Code::findOrFail($id);
    $qr->delete();

    $qrCodes = QR_Code::latest()->paginate(10);
    $view = view('reference.qr_code.table', compact('qrCodes'))->render();

    return response()->json([
        'success' => true,
        'html' => $view
    ]);
}

}
