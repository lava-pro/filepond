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

        if ($id = Filepond::storeFiles($images, $extra)) {
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
    public function listAction(int $id)
    {
        $images = Filepond::batchThumbs($id);

        return view('filepond::test.list', compact('images', 'id'));
    }

    /**
     * Show editing form
     *
     * @param  int  $id  Batch ID
     * @return View
     */
    public function editAction(int $id)
    {
        $files = Filepond::loadUploadedFiles($id);

        return view('filepond::test.edit', compact('files'));
    }

    /**
     * Update images list
     *
     * @param Request $request
     * @return mixed
     */
    public function updateAction(Request $request)
    {
        $field = Filepond::getField();

        if (! $images = $request->input($field)) {
            return redirect("filepond");
        }

        if ($id = Filepond::updateUploadedFiles($images)) {
            return redirect("filepond/list/$id");
        }
        return response('', 204);
    }

}
