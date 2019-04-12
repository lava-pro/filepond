<?php

namespace Lava\Filepond\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Lava\Filepond\Facades\Filepond;

class TestController extends Controller
{
    public function index()
    {
        return view('filepond::test');
    }

    public function handler(Request $request)
    {
        $field = Filepond::getField();

        if (! $images = $request->input($field)) {
            return response('', 204);
        }

        if ($results = Filepond::filesProcessing($images)) {
            return implode(',', $results);
        }
        return 'Empty...';

        //return redirect('filepond');
    }

}
