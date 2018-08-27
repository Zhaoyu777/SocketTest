<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Admin extends Model
{
    public function createUser()
    {
        return DB::table('user')->insert(['userName'=> 'hzy']);
    }
}
