<?php

namespace Lava\Filepond\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Lava\Filepond\Facades\Filepond;
use Illuminate\Support\Facades\DB;

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

        $extra = [
            // Resource Id
            'res_id'   => DB::table('images')->max('resource_id') + 1,
            // Resource name
            'res_name' => 'SomeModel::class',
            // User Id
            'user_id'  => rand(1, 9)
        ];

        if ($results = Filepond::filesProcessing($images, $extra)) {
            return implode(',', $results);
        }
        return response('', 204);

        //return redirect('filepond');
    }

}
