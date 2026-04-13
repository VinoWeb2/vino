<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListeAchatController extends Controller
{
    public function index(){
        return view('liste-achat.index');
    }

    public function create(){
        return view('liste-achat.create');
    }
}
