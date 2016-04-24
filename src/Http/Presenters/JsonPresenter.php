<?php
namespace Jihe\Http\Presenters;

use Jihe\Exceptions\ExceptionCode;

/**
 * Application level presenter for sending responses to clients in JSON format.
 *
 * The JSON response is composed of following parts:
 * 1. code
 *    It is the application status code, which is typically consumed by client - subsequent
 *    actions can be taken based on it.
 * 2. message
 *    Besides the status code, there is a human readable message.
 * 3. data
 *    There might be some data that should be responded as well, which is definitely
 *    business specific. This part is totally optional.
 */
trait JsonPresenter
{
    /**
     * respond given value as JSON
     *
     * @param string $message   human-readable error message
     * @param array  $data      data to respond
     * @param int    $code      application status code
     * @param int    $status    http status code
     * @param array  $headers   http response headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondAsJson($message, $data = null, $code = ExceptionCode::NO_EXCEPTION,
                                     $status = 200, $headers = [])
    {
        $response = array_filter([
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], function ($value) {
            return !is_null($value);
        });

        return response()->json($response, $status, $headers);
    }

    /**
     * respond exception as JSON
     *
     * @param mixed $data     exception, can be array|string|\Exception
     *                        for arrays, 'message' will be extracted and used as
     *                        the human-readable error message
     * @param int $code       application status code
     * @param int $status     http status code
     * @param array $headers  http response headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondExceptionAsJson($data, $code = ExceptionCode::GENERAL,
                                              $status = 200, $headers = [])
    {
        // extract code, message and data to respond from different kind of data
        $message = null;
        if ($data instanceof \Exception) { // exception object
            $message = $data->getMessage();
            $code = $data->getCode();

            if (config('app.debug')) {
                $data = [ 'trace' => $data->getTrace() ];
            } else {
                $data = null;
            }
        } else if (is_array($data)) { // array
            $message = array_get($data, 'message', '');
            array_forget($data, [ 'message' ]);
        } else { // string
            $message = $data;
            $data = null;
        }

        return $this->respondAsJson($message, $data, $code ?: ExceptionCode::GENERAL, $status, $headers);
    }
}