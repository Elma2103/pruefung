<?php
// Einbinden der Konfigurationsdatei und der Verbindung zur Datenbank
require ("includes/config.inc.php");
require ("includes/conn.inc.php");

// SQL-Abfrage, um alle relevanten Daten aus den Tabellen abzurufen
$sql = "SELECT
    tbl_einsatz.Startzeitpunkt,
    tbl_einsatz.Endzeitpunkt,
    TIMESTAMPDIFF(SECOND, tbl_einsatz.Endzeitpunkt, tbl_einsatz.Startzeitpunkt) / 3600  AS Stunden,
    (TIMESTAMPDIFF(SECOND, tbl_einsatz.Endzeitpunkt, tbl_einsatz.Startzeitpunkt) / 3600) * 60  AS Kosten,
    tbl_kunden.Vorname,
    tbl_kunden.Nachname
FROM tbl_einsatz
INNER JOIN tbl_kunden ON tbl_einsatz.FIDKunde = tbl_kunden.IDKunde";

// Die SQL-Abfrage ausführen
$result = $conn->query($sql);

// Überprüfen, ob Datensätze gefunden wurden
if ($result->num_rows > 0) {
    // Variable für den vorherigen Kunden initialisieren
    $previousCustomer = null;

    // Ausgabe der Rechnungsdaten
    while ($row = $result->fetch_assoc()) {
        // Kundendaten nur einmal ausgeben
        if ($row["Vorname"] !== $previousCustomer) {
            echo $row["Vorname"] . "<br>";
            $previousCustomer = $row["Vorname"];
        }
        
        // Ausgabe der Stunden, Startzeitpunkt, Endzeitpunkt und Kosten
        echo $row["Stunden"] . "<br>";
        echo $row["Startzeitpunkt"] . "<br>";
        echo $row["Endzeitpunkt"] . "<br>";
        echo $row["Kosten"] . "<br>";
    }
} else {
    echo "Keine Daten gefunden";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
