<?php

namespace Lava\Filepond\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    /**
     * Find items by batch id
     *
     * @param  integer $id Batch ID
     * @return array
     */
    public function findByBatchId(int $id)
    {
        return $this->where('batch_id', $id)->get();
    }
}
