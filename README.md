# Cartographic Cache

Cartographic Cache è un sistema di Reverse Proxy e Caching Privacy Friendly sviluppato in PHP per la gestione delle mappe di OpenStreetMap.

## Funzionamento

OpenStreetMap è un progetto collaborativo che ha l'obiettivo di realizzare un sistema di mappature e cartografie dell'intero pianeta. A differenza di altri servizi più conosciuti e largamente usati, questo è costituito dalla comunità che contribuisce e mantiene i dati su strade, sentieri, stazioni, luoghi pubblici etc... Per maggiori dettagli visita la pagina di [About](https://www.openstreetmap.org/about)

Le mappe sono composte da migliaia di foto chiamate tessere. Rispetto ad altri servizi che obbligano gli sviluppatori ad interrogare i servizi passando l'API Key, qui non è necessario perché i dati sono di pubblico dominio [(vedi licenza)](https://wiki.openstreetmap.org/wiki/OpenStreetMap_License), tuttavia il render della mappa (le tessere) no perché richiedono potenza di calcolo per essere elaborate e i server funzionano interamente grazie alle donazioni degli utenti e degli enti. 

I termini e le condizioni di OpenStreetMap [(vedi pagina)](https://operations.osmfoundation.org/policies/tiles/) vietano espressamente la distribuzione di applicazioni che fanno un uso intenso e massivo delle tessere. Chi sviluppa software che genera tanto traffico potrebbe violare questi termini e vedersi bloccato l'accesso al servizio. Inoltre è vietato fare il download massimo di tessere e Web Scraping. 

Per aiutare la comunità di OpenStreetMap e gli sviluppatori che dovessero trovarsi nella condizione di dover sviluppare applicazioni che implementano le mappe di OpenStreetMap ho sviluppato questo sistema di Reverse Proxy e Caching. 

Il suo funzionamento è molto semplice, bisogna semplicemente far puntare nel TileLayer della mappa l'indirizzo dove è installato questo software: 
Quando l'utente esegue lo zoom su una determinata area della mappa (ad esempio la città di Genova) il software verifica se qualcuno ha già visualizzato in passato quella determinata area della mappa. Se sì (dentro la cartella cache ci sarà salvata l'immagine) verrà restituito il dato presente sul filesystem e non verrà effettuata nessuna chiamata al server di OpenStreetMap; se nessun utente ha mai cercato quella determinata area della mappa, il server eseguirà una chiamata per conto dell'utente (Reverse Proxy), salverà la nuova immagine sul filesystem e la restituirà al client. Chi visualizzerà nuovamente quella determinata parte di mappa vedrà il file salvato nella cartella di cache e non verrà fatta più nessuna chiamata al server di OpenStreetMap preservando le loro risorse. 

Questo sistema è anche Privacy Friendly perché grazie al Reverse Proxy le chiamate ai server di OpenStreetMap (situati nel Regno Unito e quindi fuori dall'Unione Europea) verranno fatte tramite l'Application Server e non direttamente dall'utente, in questo modo nessun dato dell'utente verrà inviato a Terze Parti e a server fuori dall'UE. 

Per la parte di Reverse Proxy ho adattato parte di codice proveniente da [No.php](https://github.com/michaelfranzl/no.php/tree/master).

## Installazione

Copia la cartella dentro il server, creare le cartelle "cache", "log" e "temp". 
Il file config.php contiene tutte le variabili relative alle configurazioni.
Volendo è possibile loggare su file e/o database gli accessi e le tipologie (Cache o Reverse Proxy) di chiamate, il file "table.sql" contiene le varie CREATE TABLE. 
Abilitando il log sul db puoi implementare un cronJob che chiama il file cronJob.php, creare un Bot Telegram e ricevere ogni ora le statistiche d'utilizzo. 

## Bom / Diba

Il codice è scritto in php nativo, non sono stati utilizzati framework. 

## Licenza
Il codice sorgente viene rilasciato con licenza [MIT](https://github.com/RiccardoRiggi/cartographic-cache/blob/main/LICENSE). 
Le varie esensioni di php utilizzate mantengono le loro relative licenze.
I dati di OpenStreetMap adottano questa [licenza](https://www.openstreetmap.org/copyright)

## Garanzia limitata ed esclusioni di responsabilità

Il software viene fornito "così com'è", senza garanzie. Riccardo Riggi non concede alcuna garanzia per il software e la relativa documentazione in termini di correttezza, accuratezza, affidabilità o altro. L'utente si assume totalmente il rischio utilizzando questo applicativo.