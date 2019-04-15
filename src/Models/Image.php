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

    /**
     * Find items by transfer keys array
     *
     * @param  array $keys  Transef IDs
     * @return array
     */
    public function findByTransferKeys(array $keys)
    {
        return $this->whereIn('transfer_key', $keys)->get();
    }
}
