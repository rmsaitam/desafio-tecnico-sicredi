<?php

namespace App\Http\Controllers\v1;

use App\Enums\HttpStatusCodeEnum;
use App\Http\Requests\StoreAssociateRequest;
use App\Http\Requests\UpdateAssociateRequest;
use App\Http\Resources\AssociateResource;
use App\Repositories\AssociateRepository;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Class AssociateController
 * @package App\Http\Controllers
 */
class AssociateController extends Controller
{
    /** @var AssociateRepository */
    private $repository;

    /**
     * AssociateController constructor.
     * @param AssociateRepository $repository
     */
    public function __construct(AssociateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/associates",
     *     tags={"Listar"},
     *     summary="Listar Associados",
     *     operationId="associateList",
     *
     *     @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

     /**
      * Lista Associados
      *
     * @return JsonResponse
     */
    public function index()
    {
        $associates = $this->repository->getAll();

        return response()->json(
            AssociateResource::collection($associates),
            HttpStatusCodeEnum::SUCCESS
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/associates/1",
     *     tags={"Exibir"},
     *     summary="Exibir um Associado",
     *     operationId="associateShow",
     *
     *     @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Associado não Encontrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Exibe um Associado
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $associate = $this->repository->findByID($id);

        return response()->json(new AssociateResource($associate), HttpStatusCodeEnum::SUCCESS);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/associates",
     *     tags={"Cadastrar"},
     *     summary="Cadastrar um Associado",
     *     operationId="associateStore",
     *
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              minLength=3,
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="document",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              minLength=11,
     *              maxLength=11,
     *          )
     *      ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=400,
     *          description="Dados inválidos",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=403,
     *          description="Documento já cadastrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Cadastra novo Associado
     *
     * @param StoreAssociateRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function store(StoreAssociateRequest $request)
    {
        $associate = $this->repository->create($request->all());

        return response()->json(new AssociateResource($associate), HttpStatusCodeEnum::CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/associates/1",
     *     tags={"Atualizar"},
     *     summary="Atualizar um Associado",
     *     operationId="associateUpdate",
     *
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=3,
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="document",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=11,
     *              maxLength=11,
     *          )
     *      ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Sucesso",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=400,
     *          description="Dados inválidos",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=403,
     *          description="Documento já cadastrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Associado não Encontrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Atualiza um Associado
     *
     * @param UpdateAssociateRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateAssociateRequest $request, int $id)
    {
        $this->repository->update($id, $request->all());
        $associate = $this->repository->findByID($id);

        return response()->json(new AssociateResource($associate), HttpStatusCodeEnum::SUCCESS);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/associates/1",
     *     tags={"Excluir"},
     *     summary="Excluir um Associado",
     *     operationId="associateDestroy",
     *
     *     @OA\Response(
     *          response=204,
     *          description="Sucesso",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Associado não Encontrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Exclui um Associado
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(int $id)
    {
        $this->repository->delete($id);

        return response()->json(null, HttpStatusCodeEnum::NO_CONTENT);
    }
}
