<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    protected $errorCode;
    public function __construct(string $message = "", int $errorCode = 0, int $code = 400)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, $code);
    }

    public function render(Request $request)
    {
        $data = ['message' => $this->message, 'error_code' => $this->errorCode];
        if ($request->expectsJson()) {
            // json() 方法第二个参数就是 Http 返回码
            return response()->json($data, $this->code);
        }

        return view('pages.error', $data);
    }
}
