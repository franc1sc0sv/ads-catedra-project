<?php

namespace App\Http\Controllers;

use App\Models\Sitios_Turisticos;

class IndexController extends Controller
{
    public function Index()
    {
        // $sitio = Sitios_Turisticos::all(); no
        // return view('Index:Turi', compact('sitio')); no
    }
}
