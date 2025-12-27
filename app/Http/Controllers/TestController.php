<?php

namespace App\Http\Controllers;

use App\Models\Orphanage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TestController extends Controller
{
    public function List()
    {
        $orphanages = Orphanage::all();

        $arr = [];

        foreach ($orphanages as $o) {
            array_push($arr, $o->datas['total_children']);
        }

        dd($arr);
    }
}
