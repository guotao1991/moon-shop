<?php

namespace App\Exceptions;

use App\Traits\JsonResponse;
use App\Utils\Helper;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use JsonResponse;

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
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        //return parent::render($request, $exception);
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json($this->info(404, "请求接口错误"));
        }

        if ($exception instanceof NotesException) {
            return response()->json($this->info(304, $exception->getMessage()));
        }

        if ($exception instanceof LoginException) {
            Helper::errLog($exception, $request);
            return response()->json($this->info(202, $exception->getMessage()));
        }
        return parent::render($request, $exception);
        if ($exception instanceof Throwable) {
            Helper::errLog($exception, $request);
            return response()->json($this->info(500, "系统错误"));
        }

        return response()->json($this->info(500, "系统错误"));
    }
}
