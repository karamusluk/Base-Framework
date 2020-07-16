<?php


namespace Controllers;


use AppInit\Constants;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;

class UtilsController {
    static function CORS($app){
        // This middleware will append the response header Access-Control-Allow-Methods with all allowed methods
        $app->add(function (Request $request, RequestHandler $handler) {
            $methods = ["GET", "POST", "PUT", "PATCH", "DELETE"];
            $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
            $response = $handler->handle($request);

            $serverParams = $request->getServerParams();
            $domain = $serverParams["HTTP_ORIGIN"] ?? $serverParams["SERVER_NAME"] ?? false;
            if(in_array($domain, Constants::get("ALLOWED_DOMAINS"))) {
                $response = $response->withHeader('Access-Control-Allow-Origin', '*');
                $response = $response->withHeader('Access-Control-Allow-Methods', implode(',', $methods));
                if(!empty($requestHeaders))
                    $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);
                // Optional: Allow Ajax CORS requests with Authorization header
                $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
            }

            return $response;
        });
    }
}