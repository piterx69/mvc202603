<?php

namespace Dao\Profile;

class Me {
    private function __construct()
    {
        throw new \Exception('Clase Estatica no se puede instanciar');
    }

    public static function getProfile() : array {
        return [
            "nombre" => "Piter",
            "apellido" => "Parker",
            "correo" => "piterparker@gmail.com",
            "telefono" => "0000-0000",
        ];
    }
}