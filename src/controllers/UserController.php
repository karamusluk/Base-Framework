<?php

namespace Controllers;

use AppInit\Constants;
use Models\User;
use Rakit\Validation\Validator;


class UserController extends BaseController {

    //https://code.tutsplus.com/tr/tutorials/using-illuminate-database-with-eloquent-in-your-php-app-without-laravel--cms-27247
    public static function createUser($username, $email, $password){
        $user = User::create(['username'=>$username,'email'=>$email,'password'=>$password]);
        return $user;
    }

    public function deleteUserById($id){
        $user = User::find(1);
        $user->delete();
    }

    public function getUserById($id, $onlyAttributes = true){
        $user = User::find($id);
        if(!is_null($user)){
            return $onlyAttributes ? $user->getAttributes() : $user;
        }
        return null;
    }

    public function checkLogin($creds){
        $user = null;
        if(!empty($creds["username"]))
            $user = User::whereUsername($creds["username"]);
        else if($creds["email"])
            $user = User::whereEmail($creds["email"]);

        return $user->wherePassword($creds["password"] ?? "")->first();
    }

    public function registerUser($data){
        $validator = new Validator;
        $rules = Constants::get("REGISTER_VALIDATION_RULES");
        $rules = is_array($rules) ? $rules : [];

        $data = is_array($data) ? $data : [];

        $validation = $validator->make($data, $rules, [
            "required" => ":attribute field is required.",
            "email" => ":email is not valid.",
            "min" => ":attribute needs to be at least :min chracters."
        ]);

        $validation->setAlias("confirm_password", "Confirm Password");

        // then validate it
        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $errors->firstOfAll();
        } else {
            var_dump('');
            return "Success!";
        }
    }

    public function loginUser($data){
        $validator = new Validator;
        $rules = Constants::get("LOGIN_VALIDATION_RULES");
        $rules = is_array($rules) ? $rules : [];

        $validation = $validator->make($data, $rules, [
            "required" => ":attribute field is required.",
            "min" => ":attribute needs to be at least :min chracters."
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $errors->firstOfAll();
        } else {

            $result = $this->checkLogin([
                "username" => $data["username"] ?? false,
                "email" => $data["email"] ?? false,
                "password" => $data["password"]
            ]);

            return $result;
        }



    }

    public function getUserApiKeys($id){
        $apiKeys = User::with("apikeys")->find($id);
        if(!is_null($apiKeys)) return $apiKeys->toArray();
        return $apiKeys;
    }
}