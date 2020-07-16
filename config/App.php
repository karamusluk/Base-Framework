<?php
namespace AppInit;
use CustomMiddleware\JsonBodyParserMiddleware;
use DI\Container;
use Models\Database;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Controllers\BaseController;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class App {
    static $instance;
    static $controllerInstances;
    static function init($errorReporting = false){
        $ap = self::getInstance();

        //Instantiate Illuminate database
        new Database();
        if($errorReporting) {
            // Add Error Handling Middleware
            $ap->addErrorMiddleware(true, true, true);
            //adding php specific error printing
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');

        }

        $customErrorHandler = function (
            Psr\Http\Message\ServerRequestInterface $request,
            \Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ) use ($ap) {
            $response = $ap->getResponseFactory()->createResponse();
            if ($exception instanceof HttpNotFoundException) {
                $message = 'not found';
                $code = 404;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $message = 'not allowed';
                $code = 403;
            }

            $response->getBody()->write($message);
            return $response->withStatus($code);
        };
        $errorMiddleware = $ap->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
//        $ap->add(JsonBodyParserMiddleware::class);
        $ap->addRoutingMiddleware();

        $container = new Container();

        // Register globally to app
        $container->set('session', function () {
            return new \SlimSession\Helper();
        });

        AppFactory::setContainer($container);

        $ap->add(
            new \Slim\Middleware\Session([
                'name' => 'dummy_session',
                'autorefresh' => true,
                'lifetime' => '1 hour',
            ])
        );

        return $ap;
    }

    static function getControllerInstance($instanceName) :BaseController {

        $realInstanceName = "\Controllers\\" . $instanceName . "Controller";

        if(class_exists($realInstanceName)){

            if(!isset(self::$controllerInstances[$instanceName])){
                self::$controllerInstances[$instanceName] = new $realInstanceName();
                return self::$controllerInstances[$instanceName];
            } else{
                return self::$controllerInstances[$instanceName];
            }
        }
        return new BaseController();
    }

    static function getInstance(): \Slim\App {
        if (!isset(self::$instance)){
            self::$instance = AppFactory::create();
        }
        return self::$instance;
    }

    static function run(){
        self::getInstance()->run();
    }
}