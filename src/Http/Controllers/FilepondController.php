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
    public function upload(Request $request)
    {
        $field = Filepond::options('field');

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
     * Reverting the upload
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
     * @param  string $id File id string
     * @return Response
     */
    public function load($id = null)
    {
        if ($path = Filepond::loadLocalFile($id)) {
            return response()->file($path);
        }
        return response('', 404);
    }

    /**
     * Method:GET
     * Loads files located on remote servers
     *
     * @param  string $id File id string
     * @return mixed
     */
    public function fetch($id = null)
    {
        return '';
    }

    /**
     * Method:GET
     * Restores temporary server files
     *
     * @param  string $id File id string
     * @return mixed
     */
    public function restore($id = null)
    {
        return '';
    }

}
