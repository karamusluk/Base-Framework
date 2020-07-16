<?php


namespace Controllers;


use AppInit\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class BaseController {

    static $logFiles = ['proxy' => 'proxy.log'];
    static $logFolder = DIRECTORY_SEPARATOR."log".DIRECTORY_SEPARATOR;
    static function JSON($data, Response $response, int $code = 200 ): Response {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
    static function PLAIN_JSON($data = null, $code = 200){
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }

    static function NOT_FOUND(){
        self::PLAIN_JSON(["error" => true, "message" => "Not Found"], 404);
    }

    static function log($data, $type="proxy", $echo = true){
        $date = date("d-m-Y H:i:s");
        $format = "[$date] ";

        if(is_array($data)){
            $format .= json_encode($data);
        } else {
            $format .= $data;
        }

        $format .= PHP_EOL;

        if($echo){
            echo $format;
        } else {
            file_put_contents(Constants::get("BASEFOLDER").'/'.self::$logFolder.(self::$logFiles[$type] ?? "default.log") , $format);
        }
    }

    static function CURL($requestUrl, $data = null){
        $ch = curl_init();
        $headers["User-Agent"] = "Curl/1.0";

        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:');
        curl_setopt($ch,CURLOPT_TIMEOUT,100000);
        $response = curl_exec($ch);
        curl_close($ch);
    }
}