<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class mongoController extends Controller
{
    public function mongoConection()

    {
        
        $collection = DB::connection('mongodb')->table('actor')->get();
        return $collection;
    }
}
