<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class GradeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Grade',
            'grade' => DB::table('grade_congan')->leftJoin('kategori', 'kategori.id', '=', 'grade_congan.kategori_id')->orderBy('grade_congan.aktif', 'DESC')->orderBy('kategori.id', 'ASC')->orderBy('grade_congan.urutan', 'ASC')
                ->select('grade_congan.*', 'kategori.nm_kategori')
                ->get(),
            'kategori' => DB::table('kategori')->get()
        ];
        return view('grade.index', $data);
    }

    public function load_row(Request $r)
    {
        $data = [
            'kategori' => DB::table('kategori')->get(),
            'count' => $r->count
        ];
        return view('grade.load_row', $data);
    }

    public function save(Request $r)
    {

        for ($i = 0; $i < count($r->kategori); $i++) {
            $data = [
                'kategori_id' => $r->kategori[$i],
                'nm_grade' => $r->nm_grade[$i],
                'urutan' => $r->urutan[$i],
                'aktif' => 'Y'
            ];
            DB::table('grade_congan')->insert($data);
        }

        return redirect()->route('grade.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function non(Request $r)
    {

        for ($i = 0; $i < count($r->id); $i++) {
            DB::table('grade_congan')->where('id_grade_cong', $r->id[$i])->update([
                'aktif' => 'T'
            ]);
        }
        return redirect()->route('grade.index')->with('sukses', 'Data Berhasil DInonaktifkan');
    }

    public function getEdit(Request $r)
    {
        $data = [
            'grade' => DB::table('grade_congan')->where('id_grade_cong', $r->id)->get(),
            'kategori' => DB::table('kategori')->get()
        ];
        return view('grade.getEdit', $data);
    }
}
