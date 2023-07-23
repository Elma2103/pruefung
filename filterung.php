<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rechnungslegung_lap";

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $database);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}
function ta($in) {
	echo('<pre class="ta">');
	print_r($in);
	echo('</pre>');
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="post">
    <label for="NN">Nachname:</label>
    <input type="search" id="NN" name="NN">
    <input type="submit" name="suchen">
    
    <label for="RN">Rechnungsnummer:</label>
    <input type="search" id="RN" name="RN">
    <input type="submit" name="suchen">
</form>
</body>
</html>

<?php
     $where = ['1=1'];
     if(count($_POST)>0 && strlen($_POST["NN"])>0) {
         $where[] = "tbl_kunden.Nachname LIKE '%" . $_POST["NN"] . "%'
         ";
     }
     if(count($_POST)>0 && strlen($_POST["RN"])>0) {
         $where[] = "tbl_rechnunge.ReNo = '" . $_POST["RN"] . "'";
     }
    
$sql = "SELECT
    tbl_rechnungen.*,
    tbl_kunden.Nachname,
    tbl_kunden.Vorname,
    tbl_kunden.Adresse,
    tbl_kunden.PLZ,
    tbl_kunden.Ort,
    tbl_kunden.Emailadresse,
    tbl_staaten.Staat,
    tbl_positionen.Anzahl,
    tbl_produkte.Produkt,
    tbl_produkte.Beschreibung,
    tbl_produkte.PreisExkl,
    tbl_ustsaetze.Beschreibung AS UStSatzBeschreibung,
    (tbl_produkte.PreisExkl * tbl_ustsaetze.UStSatz / 100) AS Steuer,
    (tbl_produkte.PreisExkl * tbl_ustsaetze.UStSatz / 100 + tbl_produkte.PreisExkl) AS Preis
FROM
    tbl_rechnungen
    INNER JOIN tbl_kunden ON tbl_rechnungen.FIDKunde = tbl_kunden.IDKunde
    INNER JOIN tbl_staaten ON tbl_kunden.FIDStaat = tbl_staaten.IDStaat
    INNER JOIN tbl_positionen ON tbl_positionen.FIDRechnung = tbl_rechnungen.IDRechnung
    INNER JOIN tbl_produkte ON tbl_positionen.FIDProdukt = tbl_produkte.IDProdukt
    INNER JOIN tbl_ustsaetze ON tbl_produkte.FIDUStSatz = tbl_ustsaetze.IDUStSatz
WHERE " . implode(" AND ", $where);

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Variable für den vorherigen Kunden initialisieren
    $previousCustomer = null;

    // Ausgabe der Rechnungsdaten
    while ($row = $result->fetch_assoc()) {
        // Kundendaten nur einmal ausgeben
        if ($row["Nachname"] !== $previousCustomer) {
            echo $row["Nachname"] . "<br>";
            echo "Vorname: " . $row["Vorname"] . "<br>";
            echo "Adresse: " . $row["Adresse"] . "<br>";
            echo "PLZ: " . $row["PLZ"] . "<br>";
            echo "Ort: " . $row["Ort"] . "<br>";
            echo "Emailadresse: " . $row["Emailadresse"] . "<br>";
            echo "Staat: " . $row["Staat"] . "<br>";
            $previousCustomer = $row["Nachname"]; // Aktuellen Kunden speichern
        }

        // Rechnungspositionen ausgeben
        echo $row["ReNo"] . "<br>";
        echo $row["Datum"] . "<br>";
        echo $row["Anzahl"] . " " . $row["Produkt"] . " " . $row["Beschreibung"] . " " . $row["Preis"] . ", " . $row["Steuer"] . ", "  . "<br>";
        echo "<br>";
    }
} else {
    echo "Keine Daten gefunden";
}
$conn->close();
?>
