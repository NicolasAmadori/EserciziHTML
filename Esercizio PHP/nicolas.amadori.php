<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "giugno";

    if(!isset($_GET['A']) || !isset($_GET['B']) || !isset($_GET['O'])) {
        die("Almeno un parametro tra A, B e O non è stato settato.");
    }

    $valA = $_GET['A'];
    $valB = $_GET['B'];
    $valO = $_GET['O'];
    
    if($valA == "" || $valB == "" || $valO == "") {
        die("Almeno un parametro tra A, B e O è vuoto.");
    }

    if (!is_numeric($valA) || !is_numeric($valB) || $valA < 0 || $valB < 0) {
        die("A e B devono essere valori interi positivi.");
    }

    if ($valO != "i" && $valO != "u") {
        die("Il valore di O deve essere u o i.");
    }

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Errore di connessione al database: " . $conn->connect_error);
    }

    $maxIdResult = $conn->query("SELECT MAX(`insieme`) AS max_id FROM insiemi");
    $maxId = ($maxIdResult->fetch_assoc())['max_id'];

    if($valA > $maxId || $valB > $maxId) {
        $conn->close();
        die("Almeno uno dei due insiemi non esiste nel database.");
    }

    $stmtA = $conn->prepare("SELECT valore FROM insiemi WHERE insieme = ?");
    $stmtA->bind_param('i', $valA);
    $stmtA->execute();
    $resultA = $stmtA->get_result();

    $stmtB = $conn->prepare("SELECT valore FROM insiemi WHERE insieme = ?");
    $stmtB->bind_param('i', $valB);
    $stmtB->execute();
    $resultB = $stmtB->get_result();

    if ($resultA && $resultB) {
        $valuesA = [];
        $valuesB = [];

        while ($r = $resultA->fetch_assoc()) {
            $valuesA[] = $r['valore'];
        }
        
        while ($r = $resultB->fetch_assoc()) {
            $valuesB[] = $r['valore'];
        }

        $nuovoInsieme = ($valO === 'u') ? array_unique(array_merge($valuesA, $valuesB)) : array_intersect($valuesA, $valuesB);

        $newId = $maxId + 1; //Ottenimento dell'id per il nuovo insieme

        foreach ($nuovoInsieme as $value) {
            $conn->query("INSERT INTO insiemi (`valore`, `insieme`) VALUES ($value, $newId)");
        }

        echo "Inserito con successo il nuovo insieme nel database con id: $newId.";
    }
    else {
        $conn->close();
        die("Errore nell'ottenimento dei valori dei due insiemi");
    }

    $conn->close();
?>