<?php

namespace Lava\Filepond\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Lava\Filepond\Facades\Filepond;

class FilepondController extends Controller
{
    use ValidatesRequests;

    /**
     * Method:POST
     * Asynchronously uploading files
     *
     * @param  Request $request
     * @return Response
     */
    public function transfer(Request $request)
    {
        $field = Filepond::getField();

        $data = [
            'files' => $request->file($field),
            'metas' => $request->input($field),
        ];

        if ($result = Filepond::fileTransfer($data)) {
            return response($result)
                ->header('Content-Type', 'text/plain');
        }
        return response('', 520);
    }

    /**
     * Method:DELETE
     * Reverting the transfer
     *
     * @return Response
     */
    public function revert()
    {
        if (Filepond::revertFileTransfer(
            file_get_contents('php://input'))) {
            return response('', 204);
        }
        return response('', 400);
    }

    /**
     * Method:GET
     * Loads already uploaded server files
     *
     * @param  string $id File transfer key
     * @return Response
     */
    public function load($id = null)
    {
        if ($data = Filepond::loadLocalFile($id)) {
            return response()->file($data['path'], [
                'Content-Disposition' => 'inline; filename="' . $data['name'] . '"',
            ]);
        }
        return response('', 404);
    }

}
