<?php
require '../yhteys.php';

if (!isset($_GET['id'])) {
    die("Tilan ID puuttuu.");
}

$tila_id = (int)$_GET['id'];

// Haetaan tilan nykyiset tiedot
try {
    $sql_lause = "SELECT * FROM tilat WHERE id = :id";
    $kysely = $yhteys->prepare($sql_lause);
    $kysely->bindParam(':id', $tila_id);
    $kysely->execute();
    $tila = $kysely->fetch();
    if (!$tila) {
        die("Tila ei löytynyt.");
    }
} catch (PDOException $e) {
    die("Virhe haettaessa tilaa: " . $e->getMessage());
}

// Lomakkeen käsittely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nimi = $_POST['nimi'];
    $kapasiteetti = (int)$_POST['kapasiteetti'];

    try {
        $sql_lause = "UPDATE tilat SET nimi = :nimi, kapasiteetti = :kapasiteetti WHERE id = :id";
        $kysely = $yhteys->prepare($sql_lause);
        $kysely->execute([
            ':nimi' => $nimi,
            ':kapasiteetti' => $kapasiteetti,
            ':id' => $tila_id
        ]);

        header("Location: nayta.php?id=" . $tila_id);
        exit;
    } catch (PDOException $e) {
        die("Virhe päivitettäessä tilaa: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Muokkaa tilaa</title>
    <style>
        label { display:block; margin-top:10px; }
        input { width:300px; }
        button { margin-top:15px; padding:5px 15px; }
    </style>
</head>
<body>
    <h2>Muokkaa tilaa: <?= htmlspecialchars($tila['nimi']) ?></h2>
    <form method="post">
        <label>Nimi:<input type="text" name="nimi" value="<?= htmlspecialchars($tila['nimi']) ?>"></label>
        <label>Kapasiteetti:<input type="number" name="kapasiteetti" value="<?= htmlspecialchars($tila['kapasiteetti']) ?>" min="1"></label>
        <button type="submit">Tallenna</button>
    </form>

    <p><a href="nayta.php?id=<?= $tila_id ?>">Peruuta</a></p>
</body>
</html>
