<?php
require '../yhteys.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Virheellinen tunnusnumero.");
}

$id = (int) $_GET['id'];

try {
    // Poistetaan ensin kaikki kurssit, jotka viittaavat tähän opettajaan
    $sql1 = "DELETE FROM kurssit WHERE opettaja = :id";
    $stmt1 = $yhteys->prepare($sql1);
    $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt1->execute();

    // Poistetaan sitten opettaja
    $sql2 = "DELETE FROM opettajat WHERE tunnusnumero = :id";
    $stmt2 = $yhteys->prepare($sql2);
    $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt2->execute();

    header("Location: lista.php");
    exit;

} catch (PDOException $e) {
    echo "VIRHE: " . $e->getMessage();
}
