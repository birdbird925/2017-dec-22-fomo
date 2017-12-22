<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Slider;
use App\FeaturedProduct;

class HomeController extends Controller
{
    public function index()
    {
        $slider = Slider::all();
        $featuredProduct = FeaturedProduct::all();
        return view('home', compact('slider', 'featuredProduct'));
    }
}
