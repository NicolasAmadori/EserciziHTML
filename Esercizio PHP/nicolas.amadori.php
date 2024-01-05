<?php
/*
Scrivere il codice PHP valido (ovvero che esegua correttamente su server web Apache) che legga i dati che gli sono stati inviati tramite GET nelle variabili "A", "B" e "O".
In questa pagina, occorrerà:

    Controllare che le variabili "A" e "B" non siano nulle e che siano valide, ovvero che siano numeri positivi e che sul db ci siano numeri appartenenti a quell'insieme.
    Controllare che la variabile "O" non sia nulla e che sia uguale a "i" o "u".
    Leggere tutti i numeri appartenenti a ciascun insieme (A     e B) su database e inserirli in due vettori distinti.
    Creare un nuovo vettore contenente l'unione dei due insiemi se O vale u, altrimenti dovrà contenere l'intersezione dei due insiemi.
    Inserire sul db il nuovo insieme, usando come id dell'insieme il successivo all'id massimo.
    Dovete supporre che il db esista (nome database: giugno; nome tabella: insiemi; username: root, pw: ''), che il demone mysql sia in ascolto sulla porta 3306 e che la tabella "insiemi" sia strutturata e riempita secondo le istruzioni che trovate nel file "README_DB.txt".
    Consegnato solamente il file vostraemailunibo.php

Esempi con i dati di esempio presenti nel file README_DB.txt:
Se A=1, B=2 e O=u i seguenti numeri dovranno essere inseriti all'interno del db (colonna valore): 19, 2, 14, 98, 1. Tutte queste righe avranno il valore 3 nella colonna insieme.
Se A=1, B=2 e O=i i seguenti numeri dovranno essere inseriti all'interno del db (colonna valore): 19. Questa riga avrà il valore 3 nella colonna insieme.
*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "giugno";
$tablename = "insiemi"

if(!isset($_GET['A']) || !isset($_GET['B']) || !isset($_GET['O'])) {
    die("Manca almeno un parametro tra A, B e O.");
}

$valA = $_GET['A'];
$valB = $_GET['B'];
$valO = $_GET['O'];

if (!is_numeric($valA) || !is_numeric($valB) || $valA < 0 || $valB < 0) {
    die("A e B devono essere valori interi positivi.");
}

if (is_null($valO) || ($valO != "i" && $valO != "u")) {
    die("Valore di O non corretto.");
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Errore di connessione al database: " . $conn->connect_error);
}

$resultA = $conn->query("SELECT valore FROM $tablename WHERE insieme = $A");
$resultB = $conn->query("SELECT valore FROM $tablename WHERE insieme = $B");

if ($resultA && $resultB) {
    $valuesA = [];
    $valuesB = [];

    while ($r = $resultA->fetch_assoc()) {
        $valuesA[] = $r['valore'];
    }
    
    while ($r = $resultB->fetch_assoc()) {
        $valuesB[] = $r['valore'];
    }

    $nuovoInsieme = ($O === 'u') ? array_merge($valuesA, $valuesB) : array_intersect($valuesA, $valuesB);

    //Ottenimento dell'id per il nuovo insieme
    $maxIdResult = $conn->query("SELECT MAX('id') AS max_id FROM $tablename");
    $maxId = ($maxIdResult->fetch_assoc())['max_id'];
    $newId = $maxId + 1;

    foreach ($nuovoInsieme as $value) {
        $conn->query("INSERT INTO $tablename ('valore', 'insieme') VALUES ($value, $newId)");
    }

    echo "Inserito con successo il nuovo insieme nel database con id: $newId.";
}
else {
    $conn->close();
    die("Errore nell'ottenimento dei valori dei due insiemi");
}

$conn->close();
?>