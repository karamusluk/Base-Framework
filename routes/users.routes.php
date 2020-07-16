<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use AppInit\App;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;


//$app = AppInit\App::getInstance();
$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('Hello World');
    $user = new \Models\User();
    return $response;
});

$app->get('/user[/{userID}]', function (Request $request, Response $response, array $args) use($app) {
    $userID = $args["userID"] ?: null;
    $user = App::getControllerInstance("User")->getUserById($userID);
    $response->getBody()->write(json_encode($user));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->group('/users/{userID:[0-9]+}', function (RouteCollectorProxy $group) use($app) {
    $group->map(['GET', 'POST', 'DELETE', 'PATCH', 'PUT'], '', function (Request $request, Response $response, array $args) use($app) {
        switch($request->getMethod()){
            case "GET":
                $userID = $args["userID"] ?: null;
                $user = App::getControllerInstance("User")->getUserById($userID, true);
                $response->getBody()->write(json_encode($user));
                break;
            case "POST":
                $userController = App::getControllerInstance("User");
                $user = $userController::createUser("user1","user1@example.com","user1_pass");

                $response->getBody()->write(json_encode($user));
                break;
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);

    });

    $group->get('/api-keys', function (Request $request, Response $response, $args) use($app) {
        $userID = $args['userID'] ?: null;
        $userController = App::getControllerInstance("User");
        $apiKeys = $userController->getUserApiKeys($userID);

        return $userController::JSON($apiKeys, $response);

    });


    $group->get('/reset-password', function (Request $request, Response $response, $args) use($app) {
        // Route for /users/{id:[0-9]+}/reset-password
        // Reset the password for user identified by $args['id']
        $response->getBody()->write("Reset Password");
        return $response;
    });

    // Allow preflight requests
    $group->options('', function (Request $request, Response $response): Response {
        return $response;
    });

});


$app->group('/user', function (RouteCollectorProxy $group) use($app) {
    $group->post('/register', function (Request $request, Response $response, $args) use($app) {
        $userController = App::getControllerInstance("User");

        $contents = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $request = $request->withParsedBody($contents);
        }

        $parsedBody = $request->getParsedBody();
        $result = $userController->registerUser($parsedBody);
        return $userController::JSON($result, $response);
    });

    $app->options('/register', function (Request $request, Response $response) {
        return $response;
    });

})->add(new CustomMiddleware\SessionMiddleware());

$app->group('/user', function (RouteCollectorProxy $group) use($app) {
    $group->post('/login', function (Request $request, Response $response, $args) use($app) {
        $userController = App::getControllerInstance("User");

        $contents = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $request = $request->withParsedBody($contents);
        }

        $parsedBody = $request->getParsedBody();
        $result = $userController->loginUser($parsedBody);
        return $userController::JSON($result, $response);
    });

    $app->options('/login', function (Request $request, Response $response) {
        return $response;
    });

});

/**
 * Catch-all route to serve a 404 Not Found page if none of the routes match
 * NOTE: make sure this route is defined last
 */
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    \Controllers\BaseController::NOT_FOUND();
    return $response;

});