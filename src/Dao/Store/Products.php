<?php

namespace Dao\Store;

use Dao\Table;

const STORE_PRODUCTS_SELECT_ALL = "SELECT `productId`,`productName`,`productDescription`,`productPrice`,`productImgUrl`,`productStock`,`productStatus` FROM productss;";
const STORE_PRODUCTS_SELECT_BY_ID = "SELECT `productId`,`productName`,`productDescription`,`productPrice`,`productImgUrl`,`productStock`,`productStatus` FROM productss where `productId` = :productId;";
const STORE_PRODUCTS_INSERT = "insert into  productss ( `productName`, `productDescription`, `productPrice`, `productImgUrl`, `productStock`, `productStatus`) values (:productName, :productDescription, :productPrice, :productImgUrl, :productStock, :productStatus);";
const STORE_PRODUCTS_UPDATE = "update productss set productName = :productName,productDescription = :productDescription, productPrice = :productPrice, productImgUrl = :productImgUrl, productStock = :productStock, productStatus = :productStatus where productId = :productId;";
const STORE_PRODUCTS_DELETE = "delete from productss where productId = :productId;";


class Products extends Table{
    private function __construct()
    {
        throw new \Exception('Not implemented');
    }
    private function __clone()
    {
        throw new \Exception('Not implemented');
    }

    /*
    `products` (
    `productId` int(11) NOT NULL AUTO_INCREMENT,
    `productName` varchar(255) NOT NULL,
    `productDescription` text NOT NULL,
    `productPrice` decimal(10, 2) NOT NULL,
    `productImgUrl` varchar(255) NOT NULL,
    `productStock` int(11) NOT NULL DEFAULT 0,
    `productStatus` char(3) NOT NULL,
    */

    public static function getAll() :array {
        return self::obtenerRegistros(
            STORE_PRODUCTS_SELECT_ALL
        );
    }
    public static function getById(int $productId ):array
    {
        return self::obtenerUnRegistro(
            STORE_PRODUCTS_SELECT_BY_ID,
            [
                "productId" => $productId
            ]
        );
    }
    public static function new(
        string $productName,
        string $productDescription,
        float $productPrice,
        string $productImgUrl,
        int $productStock,
        string $productStatus
    ) {
        return self::executeNonQuery(
            STORE_PRODUCTS_INSERT,
            [
                "productName" => $productName,
                "productDescription" => $productDescription,
                "productPrice" => $productPrice,
                "productImgUrl" => $productImgUrl,
                "productStock" => $productStock,
                "productStatus" => $productStatus
            ]
        );
    }

    public static function update(
        string $productName,
        string $productDescription,
        float $productPrice,
        string $productImgUrl,
        int $productStock,
        string $productStatus,
        int $productId
    ) {
        return self::executeNonQuery(
            STORE_PRODUCTS_UPDATE,
            [
                "productName" => $productName,
                "productDescription" => $productDescription,
                "productPrice" => $productPrice,
                "productImgUrl" => $productImgUrl,
                "productStock" => $productStock,
                "productStatus" => $productStatus,
                "productId" => $productId
            ]
        );
    }

    public static function delete(int $productId ):array
    {   // Sistema de produccion: Es  Preferible un soft delete
        // Campo de is_deleted;
        return self::executeNonQuery(
            STORE_PRODUCTS_DELETE,
            [
                "productId" => $productId
            ]
        );
    }
}