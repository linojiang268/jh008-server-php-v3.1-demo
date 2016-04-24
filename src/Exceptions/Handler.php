<?php
namespace Jihe\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Jihe\Http\Presenters\JsonPresenter;
use Jihe\Validation\ValidationException;

/**
 * The catch-all exception handler
 */
class Handler extends ExceptionHandler
{
    use JsonPresenter;
    
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Jihe\Exceptions\RuntimeException::class,
    ];

    /**
     * A list of the exceptions that should not be exposed.
     * Instead, another exception message will be subsititute.
     *
     * @var array
     */
    private $wontExpose = [
        \Illuminate\Session\TokenMismatchException::class => '请勿重复提交',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $ex
     * @return void
     */
    public function report(Exception $ex)
    {
        return parent::report($ex);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $ex
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $ex)
    {
        if ($this->shouldRenderAsJson($request)) {
            return $this->renderAsJson($request, $ex);
        }

        if ($ex instanceof ValidationException) {
            return redirect()->back()->withInput()->withErrors($ex->getErrors());
        }

        return parent::render($request, $ex);
    }

    /**
     * check whether JSON should be rendered for given request
     *
     * @param Request $request  http request
     * @return bool             true if JSON is requested to respond. false otherwise.
     */
    private function shouldRenderAsJson(Request $request)
    {
        // if request is XMLHttpRequest or JSON is explicitly requested
        // render the exception as JSON
        return $request->ajax() || $request->wantsJson();
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $ex
     * @return \Illuminate\Http\Response
     */
    private function renderAsJson($request, Exception $ex)
    {
        // if the exception should not be exposed, shelter it
        $shelterMessage = array_get($this->wontExpose, get_class($ex));
        if ($shelterMessage != null) {
            return $this->respondExceptionAsJson($shelterMessage, $ex->getCode());
        }

        return $this->respondExceptionAsJson($this->morphException($ex));
    }

    /**
     * @param Exception $ex
     * @return Exception
     */
    private function morphException(Exception $ex)
    {
        if ($ex instanceof ValidationException) {
            return $this->morphValidationException($ex);
        }

        return $ex;
    }

    /**
     * morph ValidationException instance to an Excetpion instance
     *
     * @param ValidationException $ex
     * @return Exception
     */
    private function morphValidationException(ValidationException $ex)
    {
        // fetch first entry of the errors ($ex->getErrors()), which is keyed by field name
        // and valued by error messages (which is also an array, as a field can violate
        // more than one validation constraints, e.g., not filled while required,
        // maximum length exceeds, etc).we only need the first error message here.
        $message = current(current($ex->getErrors()));

        return new Exception($message, $ex->getCode(), $ex);
    }
}
