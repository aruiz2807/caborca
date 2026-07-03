<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  mixed  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        $baseUrl = rtrim((string) $request->getBaseUrl(), '/');
        $target = $request->getSchemeAndHttpHost().($baseUrl === '' ? '/' : $baseUrl.'/');

        return redirect()->away($target);
    }
}
