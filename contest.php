<?php

// Processo le informazioni ricevute dal form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $photo = $_FILES["photo"];


    // Controllo che non sia già stato raggiunto il numero max di vincitori
    $winnersLimitReached = checkWinnersLimit();


    if (!$winnersLimitReached) {
        // Selezione random di vittoria
        $result = (rand(0, 1) === 0) ? 'winner' : 'loser';

        if ($result === 'winner') {
            // Incremento il numero di vincitori
            incrementWinnersCount();
        }

        // Mando a schermo il risultato
        if ($result === 'winner') {
            echo "<h1>Congratulazioni, $name $lastname!</h1>";
            echo "<p>Hai vinto!</p>";
        } else {
            echo "<h1>Mi dispiace, $name $lastname.</h1>";
            echo "<p>Hai perso!</p>";
        }
    } else {
        echo "<h1>Siamo spiacenti, ma il numero dei vincitori giornalieri è già stato raggiunto!</h1>";
    }
}

// Check per controllare che non sia stato superato il numero massimo dei vincitori (restituisce true o fals)
function checkWinnersLimit()
{
    $maxWinnersPerDay = 5;

    // Prendo la data di oggi e la formatto
    $currentDate = date('d-m-Y');

    // Prendo dal file winners.txt i dati e il numero dei vincitori
    $winnersFile = 'winners.txt';

    if (file_exists($winnersFile)) {
        $data = file_get_contents($winnersFile);
        $winners = json_decode($data, true);
        if (isset($winners[$currentDate])) {
            // Controllo se il numero massimo dei vincitori della data odierna è stato raggiunto
            if ($winners[$currentDate] >= $maxWinnersPerDay) {
                return true;
            }
        }
    }

    return false;
}

// Funzione per incrementare il numero dei vincitori di oggi
function incrementWinnersCount()
{
    $currentDate = date('d-m-Y');
    $winnersFile = 'winners.txt';

    if (file_exists($winnersFile)) {
        $data = file_get_contents($winnersFile);
        $winners = json_decode($data, true);

        // Se c'erano già vincitori incremento, se no lo imposto a 1 per far partire il conteggio
        if (isset($winners[$currentDate])) {
            $winners[$currentDate]++;
        } else {
            $winners[$currentDate] = 1;
        }
    } else {
        $winners = array($currentDate => 1);
    }

    $data = json_encode($winners);

    // Aggiorno il file
    file_put_contents($winnersFile, $data);
}


?>
