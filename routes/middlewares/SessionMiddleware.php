<?php

namespace CustomMiddleware;


use AppInit\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use SlimSession\Helper as SessionHandler;

class SessionMiddleware {
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
//        $response->getBody()->write('BEFORE');

        $excludedPaths = Constants::get("SESSION_CHECK_EXCLUDED_PATHS");
        $excludedPaths = is_array($excludedPaths) ? $excludedPaths : [];
        if(!in_array($request->getUri()->getPath(), $excludedPaths)){
            return $this->checkSession();
        }

        //$this->response = $response;

//        $response = $this->after($response);
//        $response = $handler->handle($request);
        return $response;
    }

    public function checkSession(){
        $session = new SessionHandler();
        $user = $session->get('user', false);

        if(!$user){
            return (new \Slim\Psr7\Response())->withHeader('Location', Constants::get("BASE_URL") . "/user/login")->withStatus(302);
        }
    }

    public function before(Request $request, RequestHandler $handler){
        $response = new \Slim\Psr7\Response();
        //$response->getBody()->write('BEFORE');
        return $response;
    }

    public function after(Response $response){
        $response->getBody()->write('AFTER');
        return $response;
    }
}