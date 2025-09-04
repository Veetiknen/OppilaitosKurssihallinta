<?php
require '../yhteys.php';
    
if (!isset($_GET['id'])) {
    die("Virhe: Opiskelijan tunnusnumero puuttuu.");
}

$id = (int)$_GET['id']; 

try {
    $yhteys->beginTransaction();

    // Poistetaan ensin kurssikirjautumiset
    $sql_lause = "DELETE FROM kurssikirjautumisilla WHERE opiskelija = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely->execute();

    // Sitten poistetaan itse opiskelija
    $sql_lause2 = "DELETE FROM opiskelijat WHERE opiskelija_numero = :id";
    $kysely2 = $yhteys->prepare($sql_lause2);
    $kysely2->bindParam(':id', $id, PDO::PARAM_INT);
    $kysely2->execute();

    $yhteys->commit();
    header("Location: lista.php");
    exit();
} catch (PDOException $e) {
    $yhteys->rollBack();
    die("Virhe poistettaessa opiskelijaa: " . $e->getMessage());
}
