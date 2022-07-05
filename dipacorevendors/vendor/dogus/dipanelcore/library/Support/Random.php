<?php

namespace Dipa\Support;
Class Random
{


    public static function generate($length = 10, $add_number = true, $add_special = false)
    {

        $characters = "";
        if ($add_number == true) {
            $characters .= "0123456789";
        }

        $characters .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        if ($add_special == true) {

            $characters .= "*-/+,{[]}?_()";

        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateNumber($length = 10)
    {

        $characters = "0123456789";

        $charactersLength = strlen($characters);

        $randomString = '';

        for ($i = 0; $i < $length; $i++) {

            $randomString .= $characters[rand(0, $charactersLength - 1)];

        }
        return $randomString;
    }

}