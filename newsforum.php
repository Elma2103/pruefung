<?php
// Datenbankverbindungsparameter
$servername = "localhost";
$username = "root";
$password = "";
$database = "newsforum";

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $database);

// Überprüfen der Verbindung
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

/**
 * Funktion displayCategories
 * 
 * Diese Funktion zeigt alle Einträge mit ihren Kategorien an.
 * 
 * @param int|null $parentID - Die übergeordnete Kategorie-ID (wird für die Rekursion verwendet).
 * @param mysqli $conn - Die Datenbankverbindung.
 */
function displayCategories($parentID, $conn) {
    // SQL-Abfrage, um alle Einträge mit ihren Kategorien zu erhalten
    $sql = "SELECT
        tbl_user.Vorname,
        tbl_eintraege.Eintragezeitpunkt,
        IDEintrag,
        Eintrag
    FROM tbl_eintraege
    INNER JOIN tbl_user ON tbl_eintraege.FIDUSER = tbl_user.IDUSER 
    WHERE FIDEintrag " . ($parentID === NULL ? "IS NULL" : "= $parentID");
    
    // Die SQL-Abfrage ausführen
    $result = $conn->query($sql);

    if ($result) {
        // Überprüfen, ob Datensätze gefunden wurden
        if ($result->num_rows > 0) {
            // Start einer ungeordneten Liste, um die Einträge darzustellen
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                // Eintrag und Autor ausgeben
                echo $row["Vorname"] . " schrieb am: " . $row["Eintragezeitpunkt"];
                $categoryName = $row["Eintrag"];
                echo "<li>" . $categoryName . "</li>";

                // Rekursiv die Unterkategorien anzeigen, indem die Funktion sich selbst aufruft
                displayCategories($row["IDEintrag"], $conn);
            }
            // Ende der ungeordneten Liste
            echo "</ul>";
        }
        // Ergebnisobjekt freigeben
        $result->free();
    } else {
        // Fehlermeldung bei Problemen mit der Abfrage
        echo "Fehler in der Abfrage: " . $conn->error;
    }
}

// Die Funktion displayCategories aufrufen, um alle Einträge und Kategorien anzuzeigen
// Mit dem Parameter NULL wird die oberste Ebene der Kategorien gestartet
displayCategories(NULL, $conn);

// Verbindung zur Datenbank schließen
$conn->close();
?>
