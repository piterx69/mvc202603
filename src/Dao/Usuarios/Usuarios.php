<?php

namespace Dao\Usuarios;

use Dao\Table;

class Usuarios extends Table
{
    public static function getUsuarios(
        string $partialName = "",
        string $status = "",
        string $orderBy = "",
        bool $orderDescending = false,
        int $page = 0,
        int $itemsPerPage = 10
    ) {
        $sqlstr = "SELECT u.usercod, u.useremail, u.username, u.userest, u.usertipo, case when u.userest = 'ACT' then 'Activo' when u.userest = 'INA' then 'Inactivo' when u.userest = 'BLQ' then 'Bloqueado' when u.userest = 'SUS' then 'Suspendido' else 'Sin Asignar' end as userestDsc, case when u.usertipo = 'PBL' then 'Público' when u.usertipo = 'ADM' then 'Administrador' when u.usertipo = 'AUD' then 'Auditor' else 'Sin Asignar' end as usertipoDsc
    FROM usuario u";
        $sqlstrCount = "SELECT COUNT(*) as count FROM usuario u";
        $conditions = [];
        $params = [];
        if ($partialName != "") {
            $conditions[] = "u.username LIKE :partialName";
            $params["partialName"] = "%" . $partialName . "%";
        }
        if (!in_array($status, ["ACT", "INA", "BLQ", "SUS", ""])) {
            throw new \Exception("Error Processing Request Status has invalid value");
        }
        if ($status != "") {
            $conditions[] = "u.userest = :status";
            $params["status"] = $status;
        }
        if (count($conditions) > 0) {
            $sqlstr .= " WHERE " . implode(" AND ", $conditions);
            $sqlstrCount .= " WHERE " . implode(" AND ", $conditions);
        }
        if (!in_array($orderBy, ["usercod", "useremail", "username", ""])) {
            throw new \Exception("Error Processing Request OrderBy has invalid value");
        }
        if ($orderBy != "") {
            $sqlstr .= " ORDER BY " . $orderBy;
            if ($orderDescending) {
                $sqlstr .= " DESC";
            }
        }
        $numeroDeRegistros = self::obtenerUnRegistro($sqlstrCount, $params)["count"];
        $pagesCount = ceil($numeroDeRegistros / $itemsPerPage);
        if ($page > $pagesCount - 1) {
            $page = $pagesCount - 1;
        }
        if ($page < 0) {
            $page = 0;
        }
        $sqlstr .= " LIMIT " . $page * $itemsPerPage . ", " . $itemsPerPage;

        $registros = self::obtenerRegistros($sqlstr, $params);
        return ["usuarios" => $registros, "total" => $numeroDeRegistros, "page" => $page, "itemsPerPage" => $itemsPerPage];
    }

    public static function getUsuarioById(int $usercod)
    {
        $sqlstr = "SELECT u.usercod, u.useremail, u.username, u.userest, u.usertipo FROM usuario u WHERE u.usercod = :usercod";
        $params = ["usercod" => $usercod];
        return self::obtenerUnRegistro($sqlstr, $params);
    }

    public static function insertUsuario(
        string $useremail,
        string $username,
        string $userest,
        string $usertipo
    ) {
        $sqlstr = "INSERT INTO usuario (useremail, username, userfching, userest, usertipo) VALUES (:useremail, :username, NOW(), :userest, :usertipo)";
        $params = [
            "useremail" => $useremail,
            "username" => $username,
            "userest" => $userest,
            "usertipo" => $usertipo
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function updateUsuario(
        int $usercod,
        string $useremail,
        string $username,
        string $userest,
        string $usertipo
    ) {
        $sqlstr = "UPDATE usuario SET useremail = :useremail, username = :username, userest = :userest, usertipo = :usertipo WHERE usercod = :usercod";
        $params = [
            "usercod" => $usercod,
            "useremail" => $useremail,
            "username" => $username,
            "userest" => $userest,
            "usertipo" => $usertipo
        ];
        return self::executeNonQuery($sqlstr, $params);
    }

    public static function deleteUsuario(int $usercod)
    {
        $sqlstr = "DELETE FROM usuario WHERE usercod = :usercod";
        $params = ["usercod" => $usercod];
        return self::executeNonQuery($sqlstr, $params);
    }
}
