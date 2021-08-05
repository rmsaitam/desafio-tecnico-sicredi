<?php

namespace App\Http\Controllers\v1;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *     title="Desafio Técnico Sicredi",
 *     description="Aplicação de Votação",
 *     version="1.0.0",
 *     @OA\Contact(
 *         name="Adriano Anschau",
 *         email="<adrianoanschau@gmail.com>",
 *     )
 * )
 *
 * @OA\Server(
 *     url="https://desafio-tecnico-sicredi.herokuapp.com/api/v1",
 * )
 *
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
