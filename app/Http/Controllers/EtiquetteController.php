<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

class EtiquetteController extends Controller
{
    public function index()
    {
        return view('etiquette.index');
    }

    public function generer(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'prix' => 'required|numeric',
            'nombre_impression' => 'required|integer|min:1'
        ]);

        $data = [
            'code' => $request->code,
            'prix' => number_format($request->prix, 0, ',', '.') . ' FCFA',
            'nombre' => $request->nombre_impression
        ];

        $pdf = PDF::loadView('etiquette.pdf', $data);
        
        return $pdf->download('etiquettes.pdf');
    }
} 