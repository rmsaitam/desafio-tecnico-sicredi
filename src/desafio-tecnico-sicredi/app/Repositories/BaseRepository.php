<?php

namespace App\Repositories;

use App\Exceptions\UniqueDocumentAssociateException;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Exception;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Model class for repo.
     *
     * @var string
     */
    protected $modelClass;
    /**
     * @return EloquentQueryBuilder|QueryBuilder
     */
    protected function newQuery()
    {
        return app($this->modelClass)->newQuery();
    }
    /**
     * @param EloquentQueryBuilder|QueryBuilder $query
     * @param int                               $take
     * @param bool                              $paginate
     *
     * @return EloquentCollection|Paginator|LengthAwarePaginator
     */
    protected function doQuery($query = null, $take = 15, $paginate = true)
    {
        if (is_null($query)) {
            $query = $this->newQuery();
        }

        return $query->paginate($take);

        /**
         * para versão 2 da API
         *
        if (true == $paginate) {
            return $query->paginate($take);
        }

        if ($take > 0 || false !== $take) {
            $query->take($take);
        }
        return $query->get();
         * **/
    }
    /**
     * Returns all records.
     * If $take is false then brings all records
     * If $paginate is true returns Paginator instance.
     *
     * @param int  $take
     * @param bool $paginate
     *
     * @return EloquentCollection|Paginator
     */
    public function getAll(int $take = 15, bool $paginate = true)
    {
        return $this->doQuery(null, $take, $paginate);
    }

    /**
     * Retrieves a record by his id
     * If fail is true $ fires ModelNotFoundException.
     *
     * @param int  $id
     * @param bool $fail
     *
     * @return Model
     */
    public function findByID(int $id)
    {
        return $this->newQuery()->findOrFail($id);
    }

    /**
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data)
    {
        return $this->newQuery()->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return bool
     */
    public function update(int $id, array $data)
    {
        return $this->findByID($id)->update($data);
    }

    /**
     * @param int $id
     *
     * @return bool|int|mixed|null
     * @throws Exception
     */
    public function delete(int $id)
    {
        return $this->findByID($id)->delete();
    }


}
