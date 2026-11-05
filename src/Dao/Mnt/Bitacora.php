<?php

namespace Dao\Mnt;

use Dao\Table;

class Bitacora extends Table
{
    public static function getAll(): array
    {
        $sqlstr = "SELECT `bitacoracod`, `bitacorafch`, `bitprograma`, `bitdescripcion`, `bitobservacion`, `bitTipo`, `bitusuario` FROM `bitacora`;";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getById($bitacoracod)
    {
        $sqlstr = "SELECT `bitacoracod`, `bitacorafch`, `bitprograma`, `bitdescripcion`, `bitobservacion`, `bitTipo`, `bitusuario` FROM `bitacora` WHERE `bitacoracod` = :bitacoracod;";
        $params = ["bitacoracod" => $bitacoracod];
        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insert($bitacorafch, $bitprograma, $bitdescripcion, $bitobservacion, $bitTipo, $bitusuario)
    {
        $sqlstr = "INSERT INTO `bitacora` (`bitacorafch`, `bitprograma`, `bitdescripcion`, `bitobservacion`, `bitTipo`, `bitusuario`) VALUES (:bitacorafch, :bitprograma, :bitdescripcion, :bitobservacion, :bitTipo, :bitusuario);";
        $params = [
            "bitacorafch" => $bitacorafch,
            "bitprograma" => $bitprograma,
            "bitdescripcion" => $bitdescripcion,
            "bitobservacion" => $bitobservacion,
            "bitTipo" => $bitTipo,
            "bitusuario" => $bitusuario
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function update($bitacoracod, $bitacorafch, $bitprograma, $bitdescripcion, $bitobservacion, $bitTipo, $bitusuario)
    {
        $sqlstr = "UPDATE `bitacora` SET `bitacorafch` = :bitacorafch, `bitprograma` = :bitprograma, `bitdescripcion` = :bitdescripcion, `bitobservacion` = :bitobservacion, `bitTipo` = :bitTipo, `bitusuario` = :bitusuario WHERE `bitacoracod` = :bitacoracod;";
        $params = [
            "bitacoracod" => $bitacoracod,
            "bitacorafch" => $bitacorafch,
            "bitprograma" => $bitprograma,
            "bitdescripcion" => $bitdescripcion,
            "bitobservacion" => $bitobservacion,
            "bitTipo" => $bitTipo,
            "bitusuario" => $bitusuario
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function delete($bitacoracod)
    {
        $sqlstr = "DELETE FROM `bitacora` WHERE `bitacoracod` = :bitacoracod;";
        $params = ["bitacoracod" => $bitacoracod];
        return self::executeNonQuery($sqlstr, $params);
    }
}
