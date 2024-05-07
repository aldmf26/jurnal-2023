<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GudangBjController extends Controller
{
    public function index(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/grading_bj");
        $bj = $response->object()->grading;
        $data =  [
            'title' => 'Gudang Bj',
            'bj' => $bj,
        ];
        return view('gudangbj.index', $data);
    }
    public function bk_sortir(Request $r)
    {
        $response = Http::get("https://sarang.ptagafood.com/api/apibk/grading_bj");
        $bk_sortir = $response->object()->bk_sortir;
        $data =  [
            'title' => 'Gudang Box Sortir',
            'bk_sortir' => $bk_sortir,
        ];
        return view('gudangbj.bk_sortir', $data);
    }
}
