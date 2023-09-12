<?php

include 'db.php';

if (!function_exists('inviaNotificaTelegram')) {
    function inviaNotificaTelegram($testo)
    {
        $url = "https://api.telegram.org/bot" . TOKEN_TELEGRAM . "/sendMessage?chat_id=" . ID_USER_TELEGRAM . "&parse_mode=HTML&text=" . urlencode($testo);
        @file_get_contents($url);
    }
}

$numberCacheRequestLastHour = getNumberCacheRequestLastHour();
$numberOsmRequestLastHour = getNumberOsmRequestLastHour();
$numberCacheRequestToday = getNumberCacheRequestToday();
$numberOsmRequestToday = getNumberOsmRequestToday();

insertCacheRequestStoLastDay();
deleteCacheRequestLastDay();

if ($numberCacheRequestLastHour > 0 || $numberOsmRequestLastHour > 0) {
    //DEVO INVIARE LA NOTIFICA
    $testo = " <b>Statistiche ultimi 60 minuti</b>

- Richieste OSM: <b>" . $numberOsmRequestLastHour . "</b>
- Richieste cache: <b>" . $numberCacheRequestLastHour . "</b>

<b>Statistiche del " . date("d/m/Y") . "</b>
    
- Richieste OSM: <b>" . $numberOsmRequestToday . "</b>
- Richieste cache: <b>" . $numberCacheRequestToday . "</b>";

    inviaNotificaTelegram($testo);
}
