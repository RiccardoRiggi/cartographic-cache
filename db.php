<?php

include 'config.php';

if (!function_exists('apriConnessione')) {
    function apriConnessione()
    {
        $conn = new PDO("mysql:host=" . HOST_DATABASE . ";dbname=" . NOME_DATABASE, USERNAME_DATABASE, PASSWORD_DATABASE);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("set names utf8mb4");
        return $conn;
    }
}

if (!function_exists('chiudiConnessione')) {
    function chiudiConnessione($conn)
    {
        $conn = null;
    }
}

if (!function_exists('insertCacheRequest')) {
    function insertCacheRequest($idTile)
    {

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_CACHE_REQUEST (idTile) VALUES (:idTile )";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTile', $idTile);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('insertOsmRequest')) {
    function insertOsmRequest($idTile)
    {

        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_OSM_REQUEST (idTile) VALUES (:idTile )";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idTile', $idTile);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('getNumberCacheRequestLastHour')) {
    function getNumberCacheRequestLastHour()
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_cache_request WHERE TIMESTAMPDIFF(MINUTE,eventDate,NOW()) < 60");
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0]["numero"];
    }
}

if (!function_exists('getNumberOsmRequestLastHour')) {
    function getNumberOsmRequestLastHour()
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_osm_request WHERE TIMESTAMPDIFF(MINUTE,eventDate,NOW()) < 60");
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0]["numero"];
    }
}

if (!function_exists('insertCacheRequestStoLastDay')) {
    function insertCacheRequestStoLastDay()
    {
        $sql = "INSERT INTO " . PREFISSO_TAVOLA . "_CACHE_REQUEST_STO SELECT * FROM " . PREFISSO_TAVOLA . "_CACHE_REQUEST WHERE DATE(eventDate) = DATE( NOW() - INTERVAL 1 DAY)";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('deleteCacheRequestLastDay')) {
    function deleteCacheRequestLastDay()
    {
        $sql = "DELETE FROM " . PREFISSO_TAVOLA . "_CACHE_REQUEST WHERE DATE(eventDate) = DATE( NOW() - INTERVAL 1 DAY)";
        $conn = apriConnessione();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        chiudiConnessione($conn);
    }
}

if (!function_exists('getNumberCacheRequestToday')) {
    function getNumberCacheRequestToday()
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_cache_request WHERE DATE(eventDate) = DATE(NOW())");
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0]["numero"];
    }
}

if (!function_exists('getNumberOsmRequestToday')) {
    function getNumberOsmRequestToday()
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_osm_request WHERE DATE(eventDate) = DATE( NOW())");
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0]["numero"];
    }
}

if (!function_exists('getNumberOsmRequestTotal')) {
    function getNumberOsmRequestTotal()
    {
        $conn = apriConnessione();
        $stmt = $conn->prepare("SELECT COUNT(*) as numero FROM " . PREFISSO_TAVOLA . "_osm_request_sto ");
        $stmt->execute();
        $result = $stmt->fetchAll();
        chiudiConnessione($conn);
        return $result[0]["numero"];
    }
}