<?php

namespace App\Controllers;

use NovaCore\Http\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'title' => 'NovaPHP Basic Example',
            'description' => 'Bu örnek, NovaPHP Framework\'ün temel özelliklerini gösterir.'
        ]);
    }
}
