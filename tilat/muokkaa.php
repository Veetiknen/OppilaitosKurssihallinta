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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        main {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h2 {
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #0070c0;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #005a8f;
        }

        .cancel-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #0070c0;
        }

        .cancel-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<main>
    <div class="card">
        <h2>Muokkaa tilaa: <?= htmlspecialchars($tila['nimi']) ?></h2>
        <form method="post">
            <div class="form-group">
                <label for="nimi">Nimi:</label>
                <input type="text" name="nimi" id="nimi" value="<?= htmlspecialchars($tila['nimi']) ?>">
            </div>

            <div class="form-group">
                <label for="kapasiteetti">Kapasiteetti:</label>
                <input type="number" name="kapasiteetti" id="kapasiteetti" value="<?= htmlspecialchars($tila['kapasiteetti']) ?>" min="1">
            </div>

            <button type="submit">Tallenna</button>
        </form>

        <a class="cancel-link" href="nayta.php?id=<?= $tila_id ?>">⬅ Peruuta ja palaa</a>
    </div>
</main>

</body>
</html>
