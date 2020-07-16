<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Model {
    use SoftDeletes;
    protected $table = "users";
    protected $fillable = ["username","email","password"];
    protected $primaryKey = 'id';

    public function apikeys() {
        return $this->hasMany('\Models\ApiKey');
    }


}