<?php

namespace Lava\Filepond\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Lava\Filepond\Facades\Filepond;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Show form
     *
     * @return View
     */
    public function indexAction()
    {
        return view('filepond::test.add');
    }

    /**
     * Add images to resource
     *
     * @param Request $request
     * @return mixed
     */
    public function addAction(Request $request)
    {
        $field = Filepond::getField();

        if (! $images = $request->input($field)) {
            return response('', 204);
        }

        $extra = [
            // Batch Id
            'batch_id' => DB::table('images')->max('batch_id') + 1,
            // Resource
            'resource' => 'SomeModel::class',
            // User Id
            'user_id'  => rand(1, 9)
        ];

        if ($id = Filepond::filesProcessing($images, $extra)) {
            return redirect("filepond/list/$id");
        }
        return response('', 204);
    }

    /**
     * Images list
     *
     * @param  integer $id Batch ID
     * @return mixed
     */
    public function listAction($id)
    {
        $images = Filepond::batchThumbs($id);

        return view('filepond::test.list', compact('images'));
    }

}
