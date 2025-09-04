<?php
require '../yhteys.php';

// Tarkistetaan, onko id annettu
if (!isset($_GET['id'])) {
    die("Virhe: Opiskelijan tunnusnumero puuttuu.");
}

$id = $_GET['id'];

try {
    $yhteys->beginTransaction();

    // Poistetaan ensin kurssikirjautumiset
    $sql1 = "DELETE FROM kurssikirjautumisilla WHERE opiskelija = :id";
    $stmt1 = $yhteys->prepare($sql1);
    $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt1->execute();

    // Sitten poistetaan itse opiskelija
    $sql2 = "DELETE FROM opiskelijat WHERE opiskelija_numero = :id";
    $stmt2 = $yhteys->prepare($sql2);
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();

    $yhteys->commit();
    header("Location: lista.php");
    exit();
} catch (PDOException $e) {
    $yhteys->rollBack();
    die("Virhe poistettaessa opiskelijaa: " . $e->getMessage());
}

catch (PDOException $e) {
    die("Virhe poistettaessa opiskelijaa: " . $e->getMessage());
}
