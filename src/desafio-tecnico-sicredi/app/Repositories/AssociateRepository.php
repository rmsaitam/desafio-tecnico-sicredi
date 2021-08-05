<?php

namespace App\Repositories;

use App\Exceptions\UniqueDocumentAssociateException;
use App\Models\Associate;
use Illuminate\Database\Eloquent\Model;

class AssociateRepository extends BaseRepository
{

    /** @var string */
    protected $modelClass = Associate::class;

    /**
     * @param string $document
     * @return Model|null
     */
    public function findByDocument(string $document)
    {
        return $this->newQuery()->where('document', $document)->first();
    }

    /**
     * @param array $data
     *
     * @return Model
     * @throws UniqueDocumentAssociateException
     */
    public function create(array $data)
    {
        $consulting = $this->newQuery()->where('document', $data['document'])->get();

        if ($consulting->isNotEmpty()) {
            throw new UniqueDocumentAssociateException();
        }

        return parent::create($data);

    }
}
