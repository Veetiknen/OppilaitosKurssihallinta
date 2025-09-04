<?php
require '../yhteys.php';

// Tarkistetaan, onko id annettu
if (!isset($_GET['id'])) {
    die("Virhe: Kurssin ID puuttuu.");
}

$id = $_GET['id'];

// Estetään poistamasta testikurssia (id=1)
if ($id === 1) {
    die("Tätä testikurssia ei voi poistaa.");
}

try {
    $yhteys->beginTransaction();

    // Poistetaan ensin kurssiin liittyvät kirjautumiset
    $sql_lause = "DELETE FROM kurssikirjautumisilla WHERE kurssi = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();

    // Poistetaan itse kurssi
    $sql_lause2 = "DELETE FROM kurssit WHERE id = :id";
    $kysely = $yhteys->prepare($sql_lause2);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();

    $yhteys->commit();

    // Ohjataan takaisin listaan
    header("Location: lista.php");
    exit();
} catch (PDOException $e) {
    $yhteys->rollBack();
    die("Virhe poistettaessa kurssia: " . $e->getMessage());
}
