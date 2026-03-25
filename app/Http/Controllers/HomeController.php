<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service_Record;
use App\Models\Item;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            // Count of service records that require attention
            'item_service_required' => Service_Record::whereIn('status', ['scheduled', 'under repair', 'cancelled'])->count(),

            // Total stock in items table
            'total_stock' => Item::sum('total_stock'),

            // Total remaining in items table
            'total_remaining' => Item::sum('remaining'),
        ]);
    }
}
