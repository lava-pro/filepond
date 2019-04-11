<?php

namespace Lava\Filepond;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class Filepond
{
    /**
     * Config options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Set options
     *
     * @return void
     */
    public function __construct()
    {
        $this->options = Config::get('filepond');
    }

    /**
     * Handle file transfer
     *
     * @param  array  $data
     * @return string
     */
    public function fileTransfer(array $data)
    {
        $uniqID    = md5(uniqid(rand(1, 99), true));
        $directory = $this->options['tudn'] . '/' . $uniqID;

        // We are have twoo images (fullsize & resized)
        foreach ($data['files'] as $key => $file)
        {
            $ext  = $file->extension();
            $type = ($key === 0) ? 'imax' : 'imin';
            $name = $this->options[$type] . '.' . $ext;
            Storage::putFileAs($directory, $file, $name);
        }

        if ($metadata = $data['metas'])
        {
            $meta = @json_decode($metadata[0]);
            $original   = $data['files'][0];
            $meta->size = $original->getClientSize();
            $meta->name = $original->getClientoriginalName();
            $meta->mime = $original->getMimeType();
            Storage::put("$directory/.metadata", @json_encode($meta));
        }

        return $uniqID;
    }

    /**
     * Handle revert file transfer
     *
     * @param  string|null $id File id string
     * @return boolean
     */
    public function revertFileTransfer($id = null)
    {
        if (! $this->validateFileId($id)) {
            return false;
        }
        return Storage::deleteDirectory($this->options['tudn'] . '/' . $id);
    }

    /**
     * Handle load local file
     *
     * @param  string|null $id File id string
     * @return mixed
     */
    public function loadLocalFile($id = null)
    {
        if (! $this->validateFileId($id)) {
            return false;
        }

        // Test...
        $path = '/' . $this->options['pudn'] . '/' . $id . '/' . $this->options['imin'] . '.jpeg';

        if (Storage::exists($path)) {
            return storage_path('app') . $path;
        }
        return false;
    }

    /**
     * Handle restore file transfer
     *
     * @param  string|null $id File id string
     * @return mixed
     */
    public function restoreFileTransfer($id = null)
    {
        if (! $this->validateFileId($id)) {
            return false;
        }
    }

    /**
     * Get config option item(s)
     *
     * @param  string|null $key
     * @return string|array
     */
    public function options($key = null)
    {
        if (is_null($key)) {
            return $this->options;
        }
        return $this->options[$key] ?? null;
    }

    /**
     * Validate file id string
     *
     * @param  string $id
     * @return boolean
     */
    protected function validateFileId($id)
    {
        if (is_null($id) || ! preg_match('/^[0-9a-f]{32}$/', $id)) {
            return false;
        }
        return true;
    }

}