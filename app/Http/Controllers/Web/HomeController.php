<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\Controller;

class HomeController extends Controller{


    public function index()
    {
        return view("web.home");
    }
}
