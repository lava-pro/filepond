<?php

namespace Lava\Filepond\Repositories;

use Illuminate\Database\Eloquent\Model;

class EloquentRepository implements RepositoryInterface
{
    /**
     * Model instance
     *
     * @var object
     */
    protected $model;

    /**
     * Create model instance
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Find items by entry id
     *
     * @param  integer $id
     * @return array
     */
    public function findByEntryId(integer $id)
    {
        return $this->model->where('entry_id', $id)->get();
    }

    /**
     * Find item by transfer key
     *
     * @param  string $key
     * @return Model
     */
    public function findByKey(string $key)
    {
        return $this->model->where('transfer_key', $key)->first();
    }

    /**
     * Find item by unique file path
     *
     * @param  string $path
     * @return Model
     */
    public function findByPath(string $path)
    {
        return $this->model->where('file_path', $path)->first();
    }

}
