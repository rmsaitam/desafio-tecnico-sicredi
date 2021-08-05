<?php


namespace App\Repositories\Contracts;


use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * @param int $take
     * @param bool $paginate
     *
     * @return EloquentCollection|Paginator
     */
    public function getAll(int $take = 15, bool $paginate = true);

    /**
     * @param int $id
     *
     * @return Model
     */
    public function findByID(int $id);

    /**
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data);

    /**
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data);

    /**
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id);
}
