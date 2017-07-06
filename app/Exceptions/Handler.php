<?php namespace App\Exceptions;

use Exception;
use Redirect;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use \App\Helpers\CmsHelper;
use \App\Helpers\ViewHelper;
use \App\Site;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {

            //check redirect url
            $route = \Route::getCurrentRoute();
            $subdomain = $route->getParameter('subdomain');

            // Set default language by initial language
            \App::singleton('user_current_language', function () {
                return \App\Language::where('initial', 1)->first()->id;
            });

            if ($subdomain != null)
                $cursite = Site::where('name', '=', $subdomain)->first();
            else
                $cursite = ViewHelper::getMainSite();

            $path = str_replace($request->root(), '', $request->fullUrl());
            $found = CmsHelper::getRedirectUrl($path);

            if ($found != null && ($found->site_id == 0 || $found->site_id == $cursite->id)) {
                return Redirect::to($found->destination_url, $found->type);
            } else {

                $file_path = 'resources/views/templates/' . $cursite->template . '/errors/404.blade.php';

                $data = ['error' => $e, 'site_id' => $cursite->id];

                if (file_exists(base_path($file_path))) {
                    return response()->view('templates.' . $cursite->template . '.errors.404', $data, 404);
                } else {
                    return response()->view('errors.404', $data, 404);
                }
            }
        }
        return parent::render($request, $e);
    }

}
