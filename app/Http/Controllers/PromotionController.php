<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display the promotions page
     */
    public function index()
    {
        // Get all active promotions grouped by category, ordered by display_order
        $promotions = Promotion::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('promotions', compact('promotions'));
    }
}
