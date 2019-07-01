<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\DB;

class User extends BaseModel implements AuthenticatableContract
{
    use Authenticatable;

    public $fillable = ['login', 'password', 'name', 'surname', 'race', 'gender', 'birthday', 'homeplace', 'selfdescription', 'avatar'];


    public function checkFriendship(User $recipient)
    {
        $id_owner = $this->id;
        $id_recipient = $recipient->id;

        if ($id_owner === $id_recipient)
            return true;

        $posts = DB::select("select * from friends where ((id_owner= ? and id_subject= ?) 
or (id_owner= ? and id_subject= ?)) and approve=1", [$id_owner,$id_recipient,$id_recipient,$id_owner]);


        return !empty($posts);
    }
}
