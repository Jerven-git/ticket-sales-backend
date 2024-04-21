<?php

namespace App\Repository\Eloquent\Base;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    /**
     * Retrieves all the instances of Model
     * 
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Retrieves a single instance of Model
     * 
     * @param int $id
     * @return null|Model
     */
    public function find(int $id):?Model;

    /**
     * Deletes a single instance of Model
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}