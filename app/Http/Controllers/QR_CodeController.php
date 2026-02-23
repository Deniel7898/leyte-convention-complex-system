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
        $qrCodes = QR_Code::with([
        'creator'
        ])->latest()->paginate(10);

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
            'code'       => $code,
            'status'     => QR_Code::STATUS_ACTIVE,
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

        $qr->markAsUsed(); // already updates updated_by

        return redirect()->route('qr_codes.index')
            ->with('success', 'QR marked as used.');
    }

    /**
     * Delete QR Code
     */
        public function destroy($id)
    {
        $qr = QR_Code::findOrFail($id);

        $qr->delete();

        $qrCodes = QR_Code::with('creator')
            ->latest()
            ->paginate(10);

        $view = view('reference.qr_code.table', compact('qrCodes'))->render();

        return response()->json([
            'success' => true,
            'html' => $view
        ]);
    }

        /**
     * Print QR Label
     */
public function printLabel($id)
{
    $qr = QR_Code::findOrFail($id);

    return view('reference.qr_code.print_label', compact('qr'));
}

}
