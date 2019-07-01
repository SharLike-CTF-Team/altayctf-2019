<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function num2word($num, $words = array('год', 'года', 'лет'))
    {
        $num = $num % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1: {
                return($words[0]);
            }
            case 2: case 3: case 4: {
            return($words[1]);
        }
            default: {
                return($words[2]);
            }
        }
    }

    public static function getAge($date)
    {
        if($date!==null) {
            $birthday_timestamp = strtotime($date);
            $age = date('Y') - date('Y', $birthday_timestamp);
            if (date('md', $birthday_timestamp) > date('md')) {
                $age--;
            }
            return $age;
        }
        return null;
    }
}
