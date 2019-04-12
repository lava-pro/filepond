<?php

namespace Lava\Filepond\Repositories;

interface RepositoryInterface
{
    public function findByEntryId(integer $id);

    public function findByKey(string $key);

    public function findByPath(string $path);
}