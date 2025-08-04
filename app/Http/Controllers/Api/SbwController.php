<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SbwController extends Controller
{
    function sbw_kotor(Request $r)
    {
        $sbw = DB::table('sbw_kotor')
            ->join('table_susut', 'sbw_kotor.nm_partai', '=', 'table_susut.ket')
            ->select('sbw_kotor.*')
            ->get();
        $response = [
            'status' => 'success',
            'message' => 'Data Sarang berhasil diambil',
            'data' => [
                'sbw' => $sbw
            ],
        ];
        return response()->json($response);
    }
}
