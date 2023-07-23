<!DOCTYPE html>
<html>
<head>
    <title>Filter nach Beginnjahr</title>
</head>
<body>
<form method="POST">
        <label for="von">Von Jahr:</label>
        <input type="text" id="von" name="von" required>
        <label for="bis">Bis Jahr:</label>
        <input type="text" id="bis" name="bis" required>
        <input type="submit" name="submit" value="Filtern">
    </form>
</body>
</html>
<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$database = "schule";

// Neue Datenbankverbindung erstellen
$conn = new mysqli($servername, $username, $password, $database);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// Überprüfen, ob das Formular abgeschickt wurde und die Eingabefelder für "von" und "bis" ausgefüllt sind
if (isset($_POST['submit']) && isset($_POST['von']) && isset($_POST['bis'])) {
    // Daten aus dem Formular erhalten
    $von = $_POST['von'];
    $bis = $_POST['bis'];

    // SQL-Abfrage, um alle relevanten Daten aus den Tabellen abzurufen und nach dem Zeitraum zu filtern
    $sql = "SELECT
        tbl_lehrgangsdurchfuehrungen.Beginnjahr,
        tbl_lehrgaenge.Lehrgang,
        tbl_semester.Semester,
        tbl_gegenstaende.Gegenstand
    FROM tbl_lehrgangsdurchfuehrungen
    LEFT JOIN tbl_lehrgaenge ON tbl_lehrgangsdurchfuehrungen.FIDLehrgang = tbl_lehrgaenge.IDLehrgang
    LEFT JOIN tbl_lehrgangsplanung ON tbl_lehrgaenge.IDLehrgang = tbl_lehrgangsplanung.FIDLehrgang
    LEFT JOIN tbl_einsaetze ON tbl_lehrgangsplanung.IDLehrgangsplanung = tbl_einsaetze.FIDLehrgangsplanung
    LEFT JOIN tbl_semester ON tbl_lehrgangsplanung.FIDSemester = tbl_semester.IDSemester
    LEFT JOIN tbl_gegenstaende ON tbl_lehrgangsplanung.FIDGegenstand = tbl_gegenstaende.IDGegenstand
    WHERE tbl_lehrgangsdurchfuehrungen.Beginnjahr BETWEEN '$von' AND '$bis'";

    // Die SQL-Abfrage ausführen
    $result = $conn->query($sql);

    // Überprüfen, ob Datensätze gefunden wurden
    if ($result->num_rows > 0) {
        // Variable für den vorherigen Lehrgangsnamen initialisieren
        $previousLehrgang = null;

        // Ausgabe der Termindaten
        while ($row = $result->fetch_assoc()) {
            // Wenn der Lehrgang sich ändert, den Lehrgangsnamen ausgeben
            if ($row["Lehrgang"] !== $previousLehrgang) {
                echo "Lehrgang: " . $row["Lehrgang"] . "<br>";
                $previousLehrgang = $row["Lehrgang"];
            }
            // Kundendaten ausgeben
            echo "Beginnjahr: " . $row["Beginnjahr"] . "<br>";
            echo "Semester: " . $row["Semester"] . "<br>";
            echo "Gegenstand: " . $row["Gegenstand"] . "<br>";
        }
    } else {
        echo "Keine Daten gefunden";
    }
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
