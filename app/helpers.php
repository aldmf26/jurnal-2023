<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

if (!function_exists('tanggal')) {
    function tanggal($tgl)
    {
        $date = explode("-", $tgl);

        $bln  = $date[1];

        switch ($bln) {
            case '01':
                $bulan = "Januari";
                break;
            case '02':
                $bulan = "Februari";
                break;
            case '03':
                $bulan = "Maret";
                break;
            case '04':
                $bulan = "April";
                break;
            case '05':
                $bulan = "Mei";
                break;
            case '06':
                $bulan = "Juni";
                break;
            case '07':
                $bulan = "Juli";
                break;
            case '08':
                $bulan = "Agustus";
                break;
            case '09':
                $bulan = "September";
                break;
            case '10':
                $bulan = "Oktober";
                break;
            case '11':
                $bulan = "November";
                break;
            case '12':
                $bulan = "Desember";
                break;
        }
        $tanggal = $date[2];
        $tahun   = $date[0];

        $strTanggal = "$tanggal $bulan $tahun";
        return $strTanggal;
    }
}

if (!function_exists('sumBk')) {
    function sumBk($kategori, $data)
    {
        return array_sum(array_column($kategori, $data));
    }
}

if (!function_exists('kode')) {
    function kode($kode)
    {
        return str_pad($kode, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('buatNota')) {
    function buatNota($tbl, $kolom)
    {
        $max = DB::table($tbl)->latest($kolom)->first();
        return empty($max) ? 1000 : $max->$kolom + 1;
    }
}


class Nonaktif
{
    public static function edit($tbl, $kolom, $kolomValue, $data)
    {
        DB::table($tbl)->where($kolom, $kolomValue)->update([
            'nonaktif' => 'Y'
        ]);

        DB::table($tbl)->insert($data);
    }

    public static function delete($tbl, $kolom, $kolomValue)
    {
        DB::table($tbl)->where($kolom, $kolomValue)->update([
            'nonaktif' => 'Y'
        ]);
    }
}

class SettingHal
{
    // Cache untuk menghindari query berulang
    private static $permissionCache = [];

    /**
     * Get all permissions untuk semua user sekaligus (Eager Loading)
     */
    public static function getAllPermissions($halaman)
    {
        $cacheKey = "permissions_page_{$halaman}";

        if (isset(self::$permissionCache[$cacheKey])) {
            return self::$permissionCache[$cacheKey];
        }

        // Query SEKALI untuk semua user
        $permissions = DB::table('permission_perpage as pp')
            ->join('permission_button as pb', 'pb.id_permission_button', '=', 'pp.id_permission_button')
            ->join('users as u', 'u.id', '=', 'pp.id_user')
            ->where('pp.permission_id', $halaman)
            ->select(
                'pp.id_user',
                'pp.id_permission_page',
                'pb.id_permission_button',
                'pb.nm_permission_button',
                'pb.jenis'
            )
            ->get()
            ->groupBy('id_user'); // Group by user_id

        self::$permissionCache[$cacheKey] = $permissions;
        return $permissions;
    }

    /**
     * Get permissions untuk 1 user dari cache
     */
    public static function getUserPermissions($halaman, $id_user)
    {
        $allPermissions = self::getAllPermissions($halaman);
        return $allPermissions->get($id_user, collect());
    }

    /**
     * Check akses halaman user
     */
    public static function akses($halaman, $id_user)
    {
        $permissions = self::getUserPermissions($halaman, $id_user);
        return $permissions->isNotEmpty() ? $permissions->first() : null;
    }

    /**
     * Get specific permission button untuk user
     */
    public static function btnHal($whereId, $id_user)
    {
        return DB::table('permission_perpage as a')
            ->join('permission_button as b', 'b.id_permission_button', '=', 'a.id_permission_button')
            ->where('a.id_permission_button', $whereId)
            ->where('a.id_user', $id_user)
            ->first();
    }

    /**
     * Get permissions by jenis (create, read, update, delete)
     */
    public static function btnSetHal($halaman, $id_user, $jenis)
    {
        $permissions = self::getUserPermissions($halaman, $id_user);
        return $permissions->where('jenis', $jenis)->values();
    }
}
