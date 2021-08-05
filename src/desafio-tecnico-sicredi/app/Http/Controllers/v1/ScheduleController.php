<?php

namespace App\Http\Controllers\v1;

use App\Enums\HttpStatusCodeEnum;
use App\Http\Requests\OpenScheduleSessionRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Requests\VoteScheduleSessionRequest;
use App\Http\Resources\ScheduleResource;
use App\Repositories\AssociateRepository;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /** @var ScheduleRepository */
    private $repository;

    /** @var AssociateRepository */
    private $associateRepository;

    /**
     * ScheduleController constructor.
     *
     * @param ScheduleRepository $repository
     * @param AssociateRepository $associateRepository
     */
    public function __construct(ScheduleRepository $repository, AssociateRepository $associateRepository)
    {
        $this->repository = $repository;
        $this->associateRepository = $associateRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/schedules",
     *     tags={"Listar"},
     *     summary="Listar Pautas",
     *     operationId="scheduleList",
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
     * Lista Pautas
     *
     * @return JsonResponse
     */
    public function index()
    {
        $schedules = $this->repository->getAll();

        return response()->json(
            ScheduleResource::collection($schedules),
            HttpStatusCodeEnum::SUCCESS
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/schedules/1",
     *     tags={"Exibir"},
     *     summary="Exibir uma Pauta",
     *     operationId="scheduleShow",
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
     *          description="Pauta não Encontrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Exibe uma Pauta
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $schedule = $this->repository->findByID($id);

        return response()->json(new ScheduleResource($schedule), HttpStatusCodeEnum::SUCCESS);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/schedules",
     *     tags={"Cadastrar"},
     *     summary="Cadastrar uma Pauta",
     *     operationId="scheduleStore",
     *
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *              minLength=3,
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
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
     * )
     */

    /**
     * Cadastra nova Pauta
     *
     * @param StoreScheduleRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreScheduleRequest $request)
    {
        $schedule = $this->repository->create($request->all());

        return response()->json(new ScheduleResource($schedule), HttpStatusCodeEnum::CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/schedules/1",
     *     tags={"Atualizar"},
     *     summary="Atualizar uma Pauta",
     *     operationId="scheduleUpdate",
     *
     *      @OA\Parameter(
     *          name="title",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
     *              minLength=3,
     *          )
     *      ),
     *
     *      @OA\Parameter(
     *          name="description",
     *          in="query",
     *          @OA\Schema(
     *              type="string",
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
     *          response=404,
     *          description="Não Encontrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Atualiza uma Pauta
     *
     * @param UpdateScheduleRequest $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function update(UpdateScheduleRequest $request, int $id)
    {
        $this->repository->update($id, $request->all());
        $schedule = $this->repository->findByID($id);

        return response()->json(new ScheduleResource($schedule), HttpStatusCodeEnum::SUCCESS);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/schedules/1",
     *     tags={"Excluir"},
     *     summary="Excluir uma Pauta",
     *     operationId="scheduleDestroy",
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
     *          description="Pauta não Encontrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Exclui uma Pauta
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

    /**
     * @OA\Put(
     *     path="/api/v1/schedules/1/openSession",
     *     tags={"Atualizar"},
     *     summary="Abrir Sessão de Votação",
     *     operationId="scheduleOpenSession",
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
     *          response=400,
     *          description="Dados inválidos",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=409,
     *          description="Já existe sessão aberta para a Pauta",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=403,
     *          description="Pauta com sessão já encerrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Pauta não Encontrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Abre nova Sessão de Votação para uma Pauta
     *
     * @param OpenScheduleSessionRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function openSession(OpenScheduleSessionRequest $request, int $id)
    {
        $schedule = $this->repository->openSession($id, $request->input('time'));

        return response()->json(
            new ScheduleResource($schedule),
            HttpStatusCodeEnum::SUCCESS
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/schedules/1/closeSession",
     *     tags={"Atualizar"},
     *     summary="Fechar Sessão de Votação",
     *     operationId="scheduleCloseSession",
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
     *          response=400,
     *          description="Dados inválidos",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=403,
     *          description="Pauta com sessão já encerrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Pauta não Encontrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Fecha a Sessão de Votação de uma Pauta
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function closeSession(int $id)
    {
        $schedule = $this->repository->closeSession($id);

        return response()->json(
            new ScheduleResource($schedule),
            HttpStatusCodeEnum::SUCCESS
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/schedules/1/vote",
     *     tags={"Atualizar"},
     *     summary="Votar em uma Sessão de Votação",
     *     operationId="scheduleVote",
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
     *          response=400,
     *          description="Dados inválidos",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=404,
     *          description="Não existe sessão aberta para a Pauta | Pauta não encontrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     *
     *     @OA\Response(
     *          response=403,
     *          description="Pauta com sessão já encerrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Adiciona um voto para uma Sessão de Votação de uma Pauta
     *
     * @param VoteScheduleSessionRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function vote(VoteScheduleSessionRequest $request, int $id)
    {
        if ($request->has('associate_id')) {
            $associate = $this->associateRepository
                ->findByID($request->input('associate_id'));
        } else if ($request->has('associate.document')) {
            $associate = $this->associateRepository->findByDocument($request->input('associate.document'));
            if (is_null($associate) && $request->has('associate.name')) {
                $associate = $this->associateRepository->create($request->input('associate'));
            }
        }

        $schedule = $this->repository->vote($id, [
            'associate' => $associate,
            'option' => $request->input('option'),
        ]);

        return response()->json(
            new ScheduleResource($schedule),
            HttpStatusCodeEnum::SUCCESS
        );
    }
}
