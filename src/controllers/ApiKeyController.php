<?php


namespace Controllers;


use Models\ApiKey;
use Models\User;

class ApiKeyController  extends BaseController {

    /**
     * @param $apiKey
     * @param $userId
     * @return \Models\ApiKey
     */
    public static function createApiKey($apiKey, $userId){
        $apiKeyModel = ApiKey::create(['apikey'=>$apiKey,'user_id'=>$userId]);
        return $apiKeyModel;
    }


    public function getUser($keyId){
        $user = ApiKey::with("user")->find($keyId);
        if(!is_null($user)) return $user->toArray();
        return $user;
    }

}