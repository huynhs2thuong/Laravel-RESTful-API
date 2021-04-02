<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @SWG\Swagger(
 *     basePath="",
 *     schemes={"http", "https"},
 *     host=L5_SWAGGER_CONST_HOST,
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="L5 Swagger API",
 *         description="L5 Swagger API description",
 *         @SWG\Contact(
 *             email="your-email@domain.com"
 *         ),
 *     )
 * )
 * @SWG\SecurityScheme(
 *   securityDefinition="ApiKeyAuth",
 *   type="apiKey",
 *   description="Bearer {access-token}",
 *   in="header",
 *   name="Authorization",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function policy()
    {
        return view('privacy_policy');
    }
}

