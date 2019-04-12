<?php

namespace Lava\Filepond;

use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class Filepond
{
    /**
     * Entry type (image, file...)
     *
     * @var string
     */
    protected $type = '';

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
    public function __construct(string $type = 'image')
    {
        $this->options = Config::get("filepond.$type");
        $this->type = $type;
    }

    /**
     * Get field name
     *
     * @return string
     */
    public function getField()
    {
        return $this->options['field'] ?? null;
    }

    /**
     * Handle file transfer
     *
     * @param  array  $data
     * @return string
     */
    public function fileTransfer(array $data)
    {
        $uniqID = md5(uniqid(rand(1, 99), true));
        $directory = $this->options['tudn'] . '/' . $uniqID;

        // We are have twoo images (fullsize & resized)
        foreach ($data['files'] as $index => $file)
        {
            $ext  = $file->extension();
            $key  = 'img_' . ($index + 1);
            $name = $this->options[$key] . '.' . $ext;
            Storage::putFileAs($directory, $file, $name);
        }

        if ($metadata = $data['metas'])
        {
            $meta = @json_decode($metadata[0]);
            $original   = $data['files'][0];
            $meta->ext  = $original->extension();
            $meta->mime = $original->getMimeType();
            $meta->name = $original->getClientoriginalName();
            $meta->size = $original->getClientSize();
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
        $path = '/' . $this->options['pudn'] . '/' . $id . '/' . $this->options['img_2'] . '.jpeg';

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
     * Moves files directory to persistent place
     * Save the uploading file information in databse
     *
     * @param  array  $keys Files IDs
     * @return integer Row id
     */
    public function filesProcessing(array $keys = [])
    {
        $results = [];

        foreach ($keys as $key)
        {
            $tempDir = $this->options['tudn'] . '/' . $key;
            $persDir = $this->options['pudn'] . '/' . $key;

            if (Storage::exists($tempDir))
            {
                Storage::move($tempDir, $persDir);
                $matadata  = $this->readFileMetadata($persDir);
                $modelName = ucfirst($this->type);
                $model = "Lava\\Filepond\\Models\\{$modelName}";
                $entry = new $model;
                $entry->transfer_key = $key;
                $entry->file_path = $this->generateFilePath();
                $entry->extension = $matadata['ext']  ?? '';
                $entry->mime_type = $matadata['mime'] ?? '';
                $entry->base_name = $matadata['name'] ?? '';
                $entry->file_size = $matadata['size'] ?? '';
                $entry->save();

                $results[] = $entry->id;
            }
        }

        return $results;
    }

    /**
     * Generates public path for file
     *
     * @return string
     */
    protected function generateFilePath()
    {
        $prefix = (new DateTime)->format('m/d/H/');
        $suffix = substr(str_shuffle("0123456789abcdefhklmnorstuvwxz"), 0, 24);
        return $prefix . $suffix;
    }

    /**
     * Reads the uploaded file metadata
     *
     * @param  string $directory
     * @return array
     */
    protected function readFileMetadata($directory)
    {
        $file = $directory . '/.metadata';

        if (Storage::exists($file))
        {
            $file = Storage::get($file);
            $data = @json_decode($file, true);
            return $data;
        }
        return [];
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