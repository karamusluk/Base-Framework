<?php


namespace Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends Model {
    use SoftDeletes;
    protected $table = "api_keys";
    protected $fillable = ["apikey","user_id"];
    protected $primaryKey = 'id';


    public function user() {
        return $this->belongsTo('\Models\User');
    }

}