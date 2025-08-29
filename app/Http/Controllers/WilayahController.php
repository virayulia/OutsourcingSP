<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravolt\Indonesia\Models\City;

class WilayahController extends Controller
{
    public function getKota($provinsi_id)
    {
        $kota = City::where('province_id', $provinsi_id)->pluck('name', 'id');
        return response()->json($kota);
    }
}
