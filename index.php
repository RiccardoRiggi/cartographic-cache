<?php

function getRequestHeaders($multipart_delimiter = NULL)
{
    $headers = array();

    foreach ($_SERVER as $key => $value) {

        if ($key != "HTTP_CLIENT_IP" && $key != "HTTP_X_FORWARDED_FOR" && $key != "REMOTE_ADDR") {

            if (preg_match("/^HTTP/", $key)) { # only keep HTTP headers
                if (
                    preg_match("/^HTTP_HOST/", $key) == 0 && # let curl set the actual host/proxy
                    preg_match("/^HTTP_ORIGIN/", $key) == 0 &&
                    preg_match("/^HTTP_CONTENT_LEN/", $key) == 0 && # let curl set the actual content length
                    preg_match("/^HTTPS/", $key) == 0
                ) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    if ($key)
                        array_push($headers, "$key: $value");
                }
            } elseif (preg_match("/^CONTENT_TYPE/", $key)) {

                $key = "Content-Type";

                if (preg_match("/^multipart/", strtolower($value)) && $multipart_delimiter) {
                    $value = "multipart/form-data; boundary=" . $multipart_delimiter;
                    array_push($headers, "$key: $value");
                } else if (preg_match("/^application\/json/", strtolower($value))) {
                    // Handle application/json
                    array_push($headers, "$key: $value");
                }
            }
        }
    }
    return $headers;
}


include 'db.php';



if (ABILITA_CORS) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Expose-Headers: *');
    header('Access-Control-Max-Age: 86400');
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'options')
        exit();
}



if (!ABILITA_SERVIZIO) {
    header('Content-Type: image/png');
    echo file_get_contents("watermark.png");
    file_put_contents("./log/cache-cartografica.log", date("d/m/Y H:i:s") . " - DISABLED " . $nomeFile . "\n", FILE_APPEND);
    exit();
}



set_time_limit(0);
$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = str_replace(PERCORSO_FILE_PHP, "", $request_uri);
$nomeFile = str_replace("/", "_", $request_uri);

if (file_exists("cache/" . $nomeFile)) {
    if (ABILITA_LOG_DB) {
        insertCacheRequest($nomeFile);
    }
    header('Content-Type: image/png');
    echo file_get_contents("cache/" . $nomeFile);
    file_put_contents("./log/cache-cartografica.log", date("d/m/Y H:i:s") . " - CACHE " . $nomeFile . "\n", FILE_APPEND);
    exit();
} else {

    $backend_url = "https://b.tile.openstreetmap.org/";
    $backend_info = parse_url($backend_url);
    $host = $_SERVER['HTTP_HOST'];
    $fp = fopen(dirname(__FILE__) . "/temp/" . 'temp-' . $nomeFile, 'w+');
    $url = $backend_url . $request_uri;

    try {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, getRequestHeaders());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); # follow redirects
        curl_setopt($curl, CURLOPT_HEADER, true); # include the headers in the output
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); # return output as string
        curl_setopt($curl, CURLOPT_TIMEOUT, 600);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $contents = curl_exec($curl); # reverse proxy. the actual request to the backend server.
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        curl_close($curl); # curl is done now
        fclose($fp);
        
        
        
        $file = file_get_contents("temp/" . "temp-" . $nomeFile);
        $file = substr($file, $header_size); # remove redirection headers
        $myfile = fopen("cache/" . $nomeFile, "w+");
        fwrite($myfile, $file);
        fclose($myfile);
        unlink("temp/" . "temp-" . $nomeFile);
        
        
        
        if (ABILITA_LOG_DB) {
            insertOsmRequest($nomeFile);
        }
        
        
        
        header('Content-Type: image/png');
        echo file_get_contents("cache/" . $nomeFile);
        file_put_contents("./log/cache-cartografica.log", date("d/m/Y H:i:s") . " - HTTP " . $nomeFile . "\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents("./log/cache-cartografica.log", date("d/m/Y H:i:s") . " - ERROR " . $nomeFile . "\n", FILE_APPEND);
        file_put_contents("./log/cache-cartografica.log", date("d/m/Y H:i:s") . " - ERROR " .  $e->getMessage() . "\n", FILE_APPEND);
    }
}