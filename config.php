<?php

/*
Questa variabile indica il percorso dello script index.php su filesystem
*/
if (!defined("PERCORSO_FILE_PHP"))
    define("PERCORSO_FILE_PHP", "");

/*
Questa variabile serve per abilitare il servizio
*/
if (!defined("ABILITA_SERVIZIO"))
    define("ABILITA_SERVIZIO", true);

/*
Questa variabile serve per abilitare i cors se FE e BE si trovano su host differenti
*/
if (!defined("ABILITA_CORS"))
    define("ABILITA_CORS", true);

/*
Queste variabili servono per abilitare i diversi tipi di log
*/
if (!defined("ABILITA_LOG_FILE"))
    define("ABILITA_LOG_FILE", true);
if (!defined("ABILITA_LOG_DB"))
    define("ABILITA_LOG_DB", true);

//Informazioni per collegare il db
if (!defined("HOST_DATABASE"))
    define("HOST_DATABASE", "localhost");
if (!defined("NOME_DATABASE"))
    define("NOME_DATABASE", "cartographic-cache");
if (!defined("USERNAME_DATABASE"))
    define("USERNAME_DATABASE", "root");
if (!defined("PASSWORD_DATABASE"))
    define("PASSWORD_DATABASE", "");

/*
Prefisso delle tavole, in questo modo è possibile avere più installazioni su un unico db. 
Dovrai cambiare i prefissi dalle tavole manualmente, il default è "au"
*/
if (!defined("PREFISSO_TAVOLA"))
    define("PREFISSO_TAVOLA", "CCP");


/*
Variabili per la gestione del BOT Telegram
*/
if (!defined("ID_USER_TELEGRAM"))
    define("ID_USER_TELEGRAM", "");
if (!defined("TOKEN_TELEGRAM"))
    define("TOKEN_TELEGRAM", "");
