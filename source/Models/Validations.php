<?php

namespace Source\Models;

final class Validations{
    public static function validarString(string $String){
        return strlen($String)>=3 && !is_numeric($String);
    }
    public static function validarEmail(string $Email){
        return filter_var($Email,FILTER_VALIDATE_EMAIL);
    }

    public static function validarInteiro(string $Inteiro){
        return filter_var($Inteiro,FILTER_VALIDATE_INT) && $Inteiro>0;
    }
}