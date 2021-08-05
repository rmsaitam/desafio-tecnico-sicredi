<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodeEnum;
use Tests\TestCase;

class NotFoundExceptionTest extends TestCase
{
    public function testDispatchExceptionModelNotFoundException()
    {
        $response = [
            'message' => trans('exceptions.Resource not found'),
        ];

        $this->get(route('associates.show', 1))
            ->assertStatus(HttpStatusCodeEnum::NOT_FOUND)
            ->assertJson($response);
    }

    public function testDispatchExceptionNotFoundHttpException()
    {
        $response = [
            'message' => trans('exceptions.Invalid route')
        ];

        $this->get('/rota-invalida')
            ->assertStatus(HttpStatusCodeEnum::NOT_FOUND)
            ->assertJson($response);
    }

    public function testDispatchExceptionMethodNotAllowedException()
    {
        $response = [
            'message' => trans('exceptions.Method not allowed for this route')
        ];

        $this->put(route('associates.index'))
            ->assertStatus(HttpStatusCodeEnum::METHOD_NOT_ALLOWED)
            ->assertJson($response);

    }
}
