<?php

namespace Lava\Filepond\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return view('filepond::test');
    }

    public function handler(Request $request)
    {
        // Handle request...

        return redirect('filepond');
    }

}
