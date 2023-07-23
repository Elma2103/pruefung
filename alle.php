<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
<!-- HTML-Formular zum Filtern von Terminen -->
<form method="post">
        <label for="dropDownList">Terminekategorie:</label>
        <select name="kategorien">
        <option value=""></option>
            <option value="Privat">Privat</option>
            <option value="Arbeit">Arbeit</option>
            <option value="Urlaub">Urlaub</option>
            <option value="Wifi">Wifi</option>
            <option value="Uni">Uni</option>
            <option value="Training">Training</option>
        </select>
        <label for="terminbezeichnung">Terminbezeichnung:</label>
        <input type="text" name="terminbezeichnung">
        <label for="NicknameOdMail">Nickname/Emailadresse:</label>
        <input type="text" name="NicknameOdMail">
        <label for="datum">Datum:</label>
        <label for="datumVon">von</label>
        <input type="date" name="datumVon">
        <label for="datumBis">bis</label>
        <input type="date" name="datumBis">
        <input type="submit" value="Filtern">
    </form>
</body>
</html>

<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = "";
$database = "termine";

$conn = new mysqli($servername, $username, $password, $database);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// Erzeugen eines WHERE-Arrays, um die Filterbedingungen dynamisch zu erstellen
$where = ['1=1'];

// Überprüfen, ob eine Kategorie im Formular ausgewählt wurde und die Bedingung dem WHERE-Array hinzufügen
if (isset($_POST["kategorien"]) && strlen($_POST["kategorien"]) > 0) {
    $where[] = "tbl_kategorien.Bezeichnung LIKE '%" . $_POST["kategorien"] . "%'";
}

// Überprüfen, ob eine Terminbezeichnung im Formular eingegeben wurde und die Bedingung dem WHERE-Array hinzufügen
if (isset($_POST["terminbezeichnung"]) && strlen($_POST["terminbezeichnung"]) > 0) {
    $terminbezeichnung = $_POST["terminbezeichnung"];
    $where[] = "tbl_termine.Bezeichnung LIKE '%" . $terminbezeichnung . "%'";
}

// Überprüfen, ob ein Nickname oder eine Emailadresse im Formular eingegeben wurde und die Bedingung dem WHERE-Array hinzufügen
if (isset($_POST["NicknameOdMail"]) && strlen($_POST["NicknameOdMail"]) > 0) {
    $nicknameOrMail = $_POST["NicknameOdMail"];
    $where[] = "(tbl_user.Nickname LIKE '%" . $nicknameOrMail . "%' OR tbl_user.Emailadresse LIKE '%" . $nicknameOrMail . "%')";
}

// SQL-Abfrage, um alle relevanten Daten aus den Tabellen abzurufen und nach den Filterkriterien zu filtern
$sql = "SELECT
    tbl_termine.Bezeichnung AS Termin,
    tbl_termine.Beginn,
    tbl_user.Nickname,
    tbl_user.Emailadresse,
    tbl_user.Vorname,
    tbl_user.Notiz,
    tbl_kategorien.Bezeichnung,
    tbl_kategorien.Farbcode,
    tbl_staaten.Bezeichnung AS Staat
FROM tbl_termine
INNER JOIN tbl_user ON tbl_termine.FIDUser = tbl_user.IDUser
INNER JOIN tbl_kategorien ON tbl_termine.FIDKategorie = tbl_kategorien.IDKategorie
INNER JOIN tbl_staaten ON tbl_termine.FIDStaat = tbl_staaten.IDStaat
WHERE " . implode(" AND ", $where) . "
ORDER BY tbl_user.Nachname DESC";

// Die SQL-Abfrage ausführen
$result = $conn->query($sql);

// Überprüfen, ob Datensätze gefunden wurden
if ($result->num_rows > 0) {
    // Variable für den vorherigen Kunden initialisieren
    $previousUser = null;

    // Ausgabe der Rechnungsdaten
    while ($row = $result->fetch_assoc()) {
        // Vollständigen Namen des aktuellen Kunden erstellen (Nickname und Emailadresse)
        $currentName = $row["Nickname"] . " (" . $row["Emailadresse"] . ")";

        // Kundendaten nur einmal ausgeben, wenn sich der Name ändert
        if ($currentName !== $previousUser) {
            echo $row["Vorname"] . "<br>";
            echo $row["Notiz"] . "<br>";
            $previousUser = $currentName; // Den aktuellen Kundennamen in $previousUser speichern, um ihn mit dem nächsten Datensatz zu vergleichen
        }

        // Termininformationen für jeden Kunden ausgeben und Hintergrundfarbe entsprechend der Kategorie anzeigen
        echo ('<div style="border:2px solid #' . $row['Farbcode'] .'">');
        echo $row["Bezeichnung"] . "<br>";
        echo $row["Termin"] . "<br>";
        echo $row["Beginn"] . "<br>";
        echo $row["Staat"] . "<br>";
        echo '</div><br>';
    }
} else {
    echo "Keine Daten gefunden";
}

// Verbindung zur Datenbank schließen
$conn->close();
?>
