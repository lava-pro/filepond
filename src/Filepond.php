<?php

namespace Lava\Filepond;

use stdClass;
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
     * File transfering process
     *
     * @param  array  $data
     * @return string
     */
    public function fileTransfer(array $data)
    {
        $prefix = $this->getRandomString(12);
        $uniqID = md5(uniqid($prefix, true));
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
     * Reverts the file transfer
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
     * Loads the data for requested local file
     *
     * @param  string $id File transfer key
     * @return mixed
     */
    public function loadLocalFile(string $id)
    {
        if (! $this->validateFileId($id)) {
            return false;
        }

        $directory = $this->options['pudn'] . '/' . $id;
        $metadata  = $this->readFileMetadata($directory);

        $name = $metadata['name'] ?? 'unknown';
        $ext  = $metadata['ext']  ?? 'jpeg';

        $path = '/' . $this->options['pudn'] . '/' . $id . '/' . $this->options['img_2'] . '.' . $ext;

        if (Storage::exists($path))
        {
            $data = [
                'path' => storage_path('app') . $path,
                'name' => $name,
            ];

            return $data;
        }
        return false;
    }

    /**
     * Move files from temp directory to persists
     * Save files data in database
     *
     * @param  array  $keys   File transfer keys
     * @param  array  $extra  Extra data
     * @return array|integer
     */
    public function storeFiles(array $keys, array $extra)
    {
        $results = [];

        foreach ($keys as $key)
        {
            $tempDir = $this->options['tudn'] . '/' . $key;
            $persDir = $this->options['pudn'] . '/' . $key;

            if (Storage::exists($tempDir))
            {
                Storage::move($tempDir, $persDir);
                $matadata = $this->readFileMetadata($persDir);
                $model    = $this->getModel();
                $model->transfer_key  = $key;
                $model->file_path     = $this->generateFilePath();
                $model->user_id       = $extra['user_id']  ?? null;
                $model->batch_id      = $extra['batch_id'] ?? null;
                $model->resource      = $extra['resource'] ?? null;
                $model->extension     = $matadata['ext']   ?? null;
                $model->mime_type     = $matadata['mime']  ?? null;
                $model->base_name     = $matadata['name']  ?? null;
                $model->file_size     = $matadata['size']  ?? null;
                $model->save();

                $results[] = $model->id;
            }
        }
        // return $results;
        return $extra['batch_id'];
    }

    /**
     * Get the batch thumbs
     *
     * @param  int $id Batch ID
     * @return array
     */
    public function batchThumbs(int $id)
    {
        $images = [];
        $model  = $this->getModel();

        if ($entries = $model->findByBatchId($id))
        {
            foreach ($entries as $entry)
            {
                $fileName = '/thumb.' . $entry->extension;
                $filePath = $entry->file_path . $fileName;
                $publicPath = 'public/' . $filePath;

                if (! Storage::exists($publicPath)) {
                    $sourcePath = $this->options['pudn'] . '/' . $entry->transfer_key . $fileName;
                    Storage::copy($sourcePath, $publicPath);
                }

                $image = new stdClass;
                $image->url = Storage::url($filePath);
                $image->title = $entry->base_name;
                $images[] = $image;
            }
        }
        return $images;
    }

    /**
     * Get transfer keys for uploaded files
     *
     * @param  int $id Batch ID
     * @return string
     */
    public function loadUploadedFiles(int $id)
    {
        $model = $this->getModel();

        if ($entries = $model->findByBatchId($id))
        {
            $keys = [];
            foreach ($entries as $entry) {
                $keys[] = $entry->transfer_key;
            }

            if (count($keys))
            {
                $files = '';
                foreach ($keys as $key) {
                    $files .= "{ source: '" . $key . "', options: { type: 'local' } },";
                }
                return "[" . rtrim($files, ',') . "]";
            }
        }
        return "[]";
    }

    /**
     * Update uploaded files
     *
     * @param  array  $keys  Files IDs
     * @return integer Batch id
     */
    public function updateUploadedFiles(array $keys)
    {
        $model = $this->getModel();

        if (! $files = $model->findByTransferKeys($keys)) {
            return false;
        }

        $file    = $files[0];
        $entries = $model->findByBatchId($file->batch_id);
        $revKeys = [];

        foreach ($entries as $entry)
        {
            if (in_array($entry->transfer_key, $keys))
            {
                $revKeys[] = $entry->transfer_key;
            }
            else
            {
                $sourceDir = $this->options['pudn'] . '/' . $entry->transfer_key;
                $publicDir = 'public/' . $entry->file_path;

                if (Storage::exists($publicDir)) {
                    Storage::deleteDirectory($publicDir);
                }

                Storage::deleteDirectory($sourceDir);
                $model->destroy($entry->id);
            }
        }

        if ($keys = array_diff($keys, $revKeys))
        {
            $extra = [
                'user_id'  => $file->user_id,
                'batch_id' => $file->batch_id,
                'resource' => $file->resource,
            ];

            return $this->storeFiles($keys, $extra);
        }
        return $file->batch_id;
    }

    /**
     * Get current Model
     *
     * @return object
     */
    protected function getModel()
    {
        $modelName = ucfirst($this->type);
        $modelStr  = "Lava\\Filepond\\Models\\{$modelName}";
        return new $modelStr;
    }

    /**
     * Generates public path for file
     *
     * @return string
     */
    protected function generateFilePath()
    {
        $prefix = (new DateTime)->format('m/d/H/');
        $suffix = $this->getRandomString();
        return $prefix . $suffix;
    }

    /**
     * Generates random string
     *
     * @param  int|integer $length
     * @return string
     */
    protected function getRandomString(int $length = 24)
    {
        return substr(str_shuffle("0123456789abcdefhklmnorstuvwxz"), 0, $length);
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