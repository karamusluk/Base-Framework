<?php


namespace AppInit;


class Constants
{
    static $constants = [
        "BASEFOLDER" => "GIVE_YOUR_INSTALLATION_FOLDER",
        "BASE_URL" => "http://givemeaproxy.pro/",
        "REGISTER_VALIDATION_RULES" => [
            'name'                  => 'required',
            'username'              => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:6',
            'confirm_password'      => 'required|same:password',
        ],
        "LOGIN_VALIDATION_RULES" => [
            'username'              => 'alpha_num',
            'email'                 => 'required_without:username',
            'password'              => 'required|min:6'
        ],
        "SESSION_CHECK_EXCLUDED_PATHS" => [
            "/user/login",
            "/user/register"
        ],
        "ALLOWED_DOMAINS" => [
            "null",
            "localhost"
        ]
    ];

    static function get($key){
        return self::$constants[$key] ?? "";
    }

}