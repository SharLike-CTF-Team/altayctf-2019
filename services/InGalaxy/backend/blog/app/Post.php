<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends BaseModel
{
    public static function getNews(int $id)
    {
        $posts = DB::select("select * from posts where id_recipient in ((select id_owner from friends where 
id_subject= ? union select id_subject from friends where id_owner= ? and approve=1)) or id_owner in ((select id_owner from friends where 
id_subject= ? union select id_subject from friends where id_owner= ? and approve=1)) or id_owner= ? order by id desc",[$id,$id,$id,$id,$id]);

        return $posts;
    }

    public static function getWall (int $id)
    {
        $posts = DB::select("select * from posts where id_recipient = ? order by id desc", [$id]);

        return $posts;
    }
}
