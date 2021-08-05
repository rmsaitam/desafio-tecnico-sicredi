<?php

namespace App\Http\Controllers\v1;

use App\Enums\HttpStatusCodeEnum;
use App\Http\Requests\VoteResultRequest;
use App\Http\Resources\VoteResource;
use App\Models\Schedule;
use App\Repositories\ScheduleRepository;
use App\Repositories\VoteRepository;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Class VoteController
 * @package App\Http\Controllers
 */
class VoteController extends Controller
{
    /** @var VoteRepository */
    private $repository;

    /** @var ScheduleRepository */
    private $scheduleRepository;

    /**
     * VoteController constructor.
     * @param VoteRepository $repository
     * @param ScheduleRepository $scheduleRepository
     */
    public function __construct(VoteRepository $repository, ScheduleRepository $scheduleRepository)
    {
        $this->repository = $repository;
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/votes",
     *     tags={"Listar"},
     *     summary="Listar votos de uma Pauta",
     *     operationId="voteList",
     *
     *      @OA\Parameter(
     *          name="schedule_id",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
     *          response=404,
     *          description="Pauta não encontrado",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Lista votos de uma Pauta
     *
     * @param VoteResultRequest $request
     *
     * @return JsonResponse
     */
    public function index(VoteResultRequest $request)
    {
        /** @var Schedule $schedule */
        $schedule = $this->scheduleRepository->findByID($request->input('schedule_id'));

        $votes = $this->repository->getAllVotes($schedule);

        return response()->json(VoteResource::collection($votes), HttpStatusCodeEnum::SUCCESS);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/votes/result",
     *     tags={"Exibir"},
     *     summary="Exibe contagem de votos de uma Pauta",
     *     operationId="voteResult",
     *
     *      @OA\Parameter(
     *          name="schedule_id",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
     *          response=404,
     *          description="Pauta não Encontrada",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *     ),
     * )
     */

    /**
     * Exibe resultado de votação de uma Pauta
     *
     * @param VoteResultRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function result(VoteResultRequest $request)
    {
        /** @var Schedule $schedule */
        $schedule = $this->scheduleRepository->findByID($request->input('schedule_id'));

        $result = $this->repository->getResult($schedule);

        return response()->json($result, HttpStatusCodeEnum::SUCCESS);

    }
}
