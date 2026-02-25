<?php

namespace App\Http\Controllers;

use App\Models\QR_Code;
use Illuminate\Http\Request;
// use SimpleSoftwareIO\QrCode\Facades\QrCode as QrGenerator;

class QR_CodeController extends Controller
{
    /**
     * Display list of QR codes
     */
    public function index()
    {
        // $qrCodes = QR_Code::with([
        //     'creator'
        // ])->latest()->paginate(10);

        $qrCodes = QR_Code::with([
            'inventoryConsumable.item',
            'inventoryNonConsumable.item'
        ])->latest()->paginate(10);

        $qrCodes_table = view('reference.qr_code.table', compact('qrCodes'))->render();
        return view('reference.qr_code.index', compact('qrCodes_table'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Generate new QR Code
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show QR Image
     */
    public function show($id)
    {
        //
    }

    /**
     * Mark as Used
     */
    public function markUsed($id)
    {
        //
    }

    /**
     * Delete QR Code
     */
    public function destroy($id)
    {
        //
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
