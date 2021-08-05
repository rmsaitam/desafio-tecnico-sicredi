<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodeEnum;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @param Exception $exception
     *
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param Request $request
     * @param Exception $exception
     *
     * @return JsonResponse
     */
    public function renderCustom(Request $request, Exception $exception)
    {
        return $exception->render();
    }

    /**
     * @return JsonResponse
     */
    public function renderModelNotFoundException()
    {
        return response()->json([
            'message' => trans('exceptions.Resource not found'),
        ], HttpStatusCodeEnum::NOT_FOUND);
    }

    /**
     * @param Validator $validator
     *
     * @return JsonResponse
     */
    public function renderValidationException(Validator $validator)
    {
        return response()->json([
            'message' => trans('exceptions.Validation error on uploaded data'),
            'errors' => $validator->errors(),
        ], HttpStatusCodeEnum::BAD_REQUEST);
    }

    /**
     * @return JsonResponse
     */
    public function renderNotFoundHttpException()
    {
        return response()->json([
            'message' => trans('exceptions.Invalid route')
        ], HttpStatusCodeEnum::NOT_FOUND);
    }

    /**
     * @return JsonResponse
     */
    public function renderMethodNotAllowedException()
    {
        return response()->json([
            'message' => trans('exceptions.Method not allowed for this route')
        ], HttpStatusCodeEnum::METHOD_NOT_ALLOWED);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Exception  $exception
     * @return Response|JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if (
            $exception instanceof ScheduleHasSessionException
            || $exception instanceof ScheduleNotHasSessionException
            || $exception instanceof ScheduleSessionIsClosedException
            || $exception instanceof UniqueVotePerSessionException
            || $exception instanceof UniqueDocumentAssociateException
        ) {
            return $this->renderCustom($request, $exception);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->renderModelNotFoundException();
        }

        if ($exception instanceof ValidationException) {
            return $this->renderValidationException($exception->validator);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->renderNotFoundHttpException();
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->renderMethodNotAllowedException();
        }

        return response()->json([
            'message' => trans('exceptions.Unknown error')
        ], HttpStatusCodeEnum::INTERNAL_SERVER_ERROR);
    }
}
