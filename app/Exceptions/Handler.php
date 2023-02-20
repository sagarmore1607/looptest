<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        switch($e){
            case ($e instanceof \Illuminate\Database\QueryException):
                $message = $e->getMessage();
                $code = $e->getCode();
                $line = $e->getLine();
                $file = $e->getFile();
                return response()->json([
                    'status' => $code,
                    'message' => $message,
                    'type' => 'QueryException'
                ]);
                break;
            
            case ($e instanceof \Exception):
                $message = $e->getMessage();
                $code = $e->getCode();
                $line = $e->getLine();
                $file = $e->getFile();
                return response()->json([
                    'status' => $code,
                    'message' => $message,
                    'type' => 'Exception'
                ]);
                break;

            default:
               return parent::render($request, $e);
               break;
        }
    }
}
