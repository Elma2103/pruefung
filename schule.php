<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$database = "schule";

$conn = new mysqli($servername, $username, $password, $database);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// SQL-Abfrage, um alle relevanten Daten aus den Tabellen abzurufen
$sql = "SELECT
    tbl_vortragende.Nachname,
    tbl_vortragende.Vorname,
    tbl_einsaetze.Stundenzahl,
    tbl_lehrgangsdurchfuehrungen.Beginnjahr,
    tbl_lehrgaenge.Lehrgang
FROM tbl_einsaetze
INNER JOIN tbl_vortragende ON tbl_einsaetze.FIDVortragender = tbl_vortragende.IDVortragender
INNER JOIN tbl_lehrgangsdurchfuehrungen ON tbl_einsaetze.FIDLehrgangsdurchfuehrung = tbl_lehrgangsdurchfuehrungen.IDLehrgangsdurchfuehrung
INNER JOIN tbl_lehrgaenge ON tbl_lehrgangsdurchfuehrungen.FIDLehrgang = tbl_lehrgaenge.IDLehrgang";

// Die SQL-Abfrage ausführen
$result = $conn->query($sql);

// Überprüfen, ob Datensätze gefunden wurden
if ($result->num_rows > 0) {
    // Variable für den vorherigen Benutzernamen initialisieren
    $previousUser = null;

    // Ausgabe der Termindaten
    while ($row = $result->fetch_assoc()) {
        // Vollständigen Namen des aktuellen Kunden erstellen (Vorname und Nachname)
        $currentName = $row["Vorname"] . " " . $row["Nachname"];

        // Kundendaten nur einmal ausgeben, wenn sich der Name ändert
        if ($currentName !== $previousUser) {
            echo $row["Vorname"] . "<br>";
            echo $row["Nachname"] . "<br>";
            $previousUser = $currentName; // Den aktuellen Kundennamen in $previousUser speichern, um ihn mit dem nächsten Datensatz zu vergleichen
        }

        // Termindaten für jeden Benutzer ausgeben
        echo $row["Stundenzahl"] . "<br>";
        echo $row["Beginnjahr"] . "<br>";
    }
} else {
    echo "Keine Daten gefunden";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
