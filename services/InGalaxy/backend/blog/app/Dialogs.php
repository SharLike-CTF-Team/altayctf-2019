<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dialogs extends BaseModel
{
    public $table = "dialogs";

    public static function generateToken($user1, $user2)
    {
        $login1 = $user1->login;
        $login2 = $user2->login;
        $token = md5(md5($login1).md5($login2));
        return $token;
    }
}
