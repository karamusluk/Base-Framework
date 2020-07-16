<?php


use AppInit\App;
use AppInit\Constants;

require __DIR__ . '/../../vendor/autoload.php';

class ProxyChecker {

    protected $proxyFileName;
    protected $proxyController;
    function __construct(){
        $this->proxyFileName =  Constants::get("BASEFOLDER"). '/opt/workers/file/proxy.list.txt';
        $this->proxyController = App::getControllerInstance("Proxy");
        $this->fetchProxies();
    }
    function fetchProxies(){
        if (!file_exists($this->proxyFileName)) {
            file_put_contents($this->proxyFileName, file_get_contents('https://raw.githubusercontent.com/fate0/proxylist/master/proxy.list'));
        } else {
            $filetime = filemtime($this->proxyFileName);
            if (time() - $filetime > 900) {
                file_put_contents($this->proxyFileName, file_get_contents('https://raw.githubusercontent.com/fate0/proxylist/master/proxy.list'));
            }
        }
    }

    function getProxies(){
        $p_list = file_get_contents($this->proxyFileName);
        $p_list = explode(PHP_EOL, $p_list);
        $proxy_list = [];
        foreach ($p_list as $key => $row) {
            if (!isset($row[$key]) || $row[$key] == '') {
                continue;
            }
            $row = json_decode($row, true);
            $row['latency'] = $row['response_time'];
            unset($row['response_time']);
            unset($row['export_address']);
            unset($row['from']);
            ksort($row);
            $proxy_list[] = $row;
        }
        return $proxy_list;
    }

    function checkDatabaseAndModify(){
        $limit = 2;
        $offset = 0;
        do{
            $proxies = $this->proxyController->getProxies($limit,$offset);
            $offset += $limit;
            foreach ($proxies as $proxy){
                $proxyModel = $proxy;
                $proxy = $proxy->getAttributes();
                $ip = $proxy["host"] ?? ($proxy["ip"] ?? -1);
                $res = $this->checkSingleProxy($ip, $proxy['port'], 20);
                if (!$res['result']['success']) {
                    $result = $this->proxyController::removeProxy($proxy);
                    $this->proxyController::log($result ? "Proxy # ".$proxy["id"]." removed." : "Deletion Error for proxy # ".$proxy["id"]);
                } else{
                    $proxyModel->touch();
                    $this->proxyController::log("Proxy # ".$proxy["id"]." is working.");
                }

            }
        } while(count($proxies) == $limit);

    }

    function checkFileAndInsert(){
        foreach ($this->getProxies() as &$p) {
            $ip = $p["host"] ?: ($p["ip"] ?: -1);
            $res = $this->checkSingleProxy($ip, $p['port'], 20);
            if ($res['result']['success']) {
                $this->proxyController::log($res);
                $proxy = [
                    'ip'=>$ip,
                    'port'=>$p['port'],
                    'type'=>$p["type"],
                    'country' => $p["country"],
                    'anonymity' => $p["anonymity"],
                    'response_time' => $p["latency"]
                ];

                $result = $this->proxyController::addProxy($proxy);
            }
        }
    }

    function checkSingleProxy($ip, $port, $timeout, $echoResults = false, $socksOnly = false, $proxy_type = "http(s)") {
        $passByIPPort = $ip . ":" . $port;

        // You can use virtually any website here, but in case you need to implement other proxy settings (show annonimity level)
        // I'll leave you with whatismyipaddress.com, because it shows a lot of info.
        $url = "http://whatismyipaddress.com/";

        // Get current time to check proxy speed later on
        $loadingtime = microtime(true);

        $theHeader = curl_init($url);
        curl_setopt($theHeader, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($theHeader, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($theHeader, CURLOPT_PROXY, $passByIPPort);

        //If only socks proxy checking is enabled, use this below.
        if ($socksOnly) {
            curl_setopt($theHeader, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }

        //This is not another workaround, it's just to make sure that if the IP uses some god-forgotten CA we can still work with it ;)
        //Plus no security is needed, all we are doing is just 'connecting' to check whether it exists!
        curl_setopt($theHeader, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($theHeader, CURLOPT_SSL_VERIFYPEER, 0);

        //Execute the request
        $curlResponse = curl_exec($theHeader);

        if ($curlResponse === false) {
            //If we get a 'connection reset' there's a good chance it's a SOCKS proxy
            //Just as a safety net though, I'm still aborting if $socksOnly is true (i.e. we were initially checking for a socks-specific proxy)
            if (curl_errno($theHeader) == 56 && !$socksOnly) {
                $this->CheckSingleProxy($ip, $port, $timeout, $echoResults, true, "socks");
                return;
            }

            $arr = array(
                "result" => array(
                    "success" => false,
                    "error" => curl_error($theHeader),
                    "proxy" => array(
                        "ip" => $ip,
                        "port" => $port,
                        "type" => $proxy_type,
                    ),
                ),
            );
        } else {
            $arr = array(
                "result" => array(
                    "success" => true,
                    "proxy" => array(
                        "ip" => $ip,
                        "port" => $port,
                        "speed" => ceil((microtime(true) - $loadingtime) * 1000),
                        "type" => $proxy_type,
                    ),
                ),
            );
        }
        if ($echoResults) {
            echo json_encode($arr);
        }
        return $arr;
    }
}