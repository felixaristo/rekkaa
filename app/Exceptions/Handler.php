<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    protected function prepareException(Exception $e)
    {
        if ($e instanceof TokenMismatchException) {
            $e = new HttpException(419, __('exception.csrf_token_mismatch'), $e);
        }

        return parent::prepareException($e);
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $message = "URL : ". $request->fullUrl()."\nMethod : ". $request->method()."\nIP Client : ". $request->ip()."\nData : ". json_encode($request->input())."\nFile : ".$exception->getFile()."\nLine : ".$exception->getLine()."\nCode : ".$exception->getCode()."\nMessage : ".$exception->getMessage();
        // if(env('APP_ENV') == 'production') {
            Log::error($message);
        // }
        // dd($exception->get);
        // $client  = new Client();
        // $url = "https://api.telegram.org/bot6849680149:AAEJC5eHCQeK-p1f_-Pji9IgOeM0rwPdMFI/sendMessage";//<== ganti jadi token yang kita tadi
        // $data    = $client->request('GET', $url, [
        //     'json' =>[
        //       "chat_id" => "-4120438741", //<== ganti dengan id_message yang kita dapat tadi
        //       "text" => "[".env('APP_URL')."]\n=======\n"."URL : ". $request->fullUrl()."\nMethod : ". $request->method()."\nIP Client : ". $request->ip()."\nData : ". json_encode($request->input())."\nFile : ".$exception->getFile()."\nLine : ".$exception->getLine()."\nCode : ".$exception->getCode()."\nMessage : ".$exception->getMessage(),"disable_notification" => true
        //     ]
        // ]);

        // $json = $data->getBody();
        // Log::info("[".env('APP_URL')."]\n=======\n"."URL : ". $request->fullUrl()."\nMethod : ". $request->method()."\nIP Client : ". $request->ip()."\nData : ". json_encode($request->input())."\nFile : ".$exception->getFile()."\nLine : ".$exception->getLine()."\nCode : ".$exception->getCode()."\nMessage : ".$exception->getMessage());
        return parent::render($request, $exception);
    }
}
