<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ContractMapController extends Controller
{
    public function index(Request $request){
        return view('contract.map');
    }
}