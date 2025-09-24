<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['opettajat'])) {
    die("Opettajaa ei valittu");
}

$opettaja_id = (int)$_GET['opettajat'];
$viikko = $_GET['viikko'] ?? date('Y-W');

// Haetaan opettajan tiedot
$sql = "SELECT etunimi, sukunimi, aine FROM opettajat WHERE tunnusnumero = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$opettaja_id]);
$opettaja = $stmt->fetch();
if (!$opettaja) die("Opettajaa ei löytynyt");

// Viikon käsittely
list($vuosi, $viikkonro) = explode('-', $viikko);
$viikon_alku = new DateTime();
$viikon_alku->setISODate($vuosi, $viikkonro);
$viikon_loppu = clone $viikon_alku;
$viikon_loppu->modify('+4 days');

$edellinen_viikko = clone $viikon_alku;
$edellinen_viikko->modify('-1 week');
$seuraava_viikko = clone $viikon_alku;
$seuraava_viikko->modify('+1 week');

// Haetaan kaikki opettajan kurssit ja sessiot
$sql = "SELECT s.viikonpaiva, s.aloitus, s.lopetus,
               k.nimi AS kurssi_nimi, k.alkupäivä, k.loppupäivä,
               t.nimi AS tila_nimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        JOIN tilat t ON k.tila = t.id
        WHERE k.opettaja = ?";
$stmt = $yhteys->prepare($sql);
$stmt->execute([$opettaja_id]);
$sessiot = $stmt->fetchAll(PDO::FETCH_ASSOC);

$viikonpaivat = ['ma'=>'Ma', 'ti'=>'Ti', 'ke'=>'Ke', 'to'=>'To', 'pe'=>'Pe'];

function onkoKurssiKaynnissa($alkupaiva, $loppupaiva, $viikon_alku, $viikon_loppu) {
    $kurssi_alku = new DateTime($alkupaiva);
    $kurssi_loppu = new DateTime($loppupaiva);
    return !($kurssi_loppu < $viikon_alku || $kurssi_alku > $viikon_loppu);
}

renderHeader("Viikkonäkymä - " . htmlspecialchars($opettaja['etunimi'] . ' ' . $opettaja['sukunimi']));
?>

<p><strong>Aine:</strong> <?= htmlspecialchars($opettaja['aine']) ?></p>
<p><strong>Viikko:</strong> <?= $viikkonro ?>/<?= $vuosi ?> (<?= $viikon_alku->format('d.m.Y') ?> - <?= $viikon_loppu->format('d.m.Y') ?>)</p>

<div class="week-navigation">
    <a href="?opettajat=<?= $opettaja_id ?>&viikko=<?= $edellinen_viikko->format('Y-W') ?>" class="btn">« Edellinen viikko</a>
    <a href="?opettajat=<?= $opettaja_id ?>&viikko=<?= date('Y-W') ?>" class="btn">Tämä viikko</a>
    <a href="?opettajat=<?= $opettaja_id ?>&viikko=<?= $seuraava_viikko->format('Y-W') ?>" class="btn">Seuraava viikko »</a>
</div>

<table class="schedule-table">
    <thead>
        <tr>
            <th class="time-header">Aika</th>
            <?php 
            $paiva_counter = 0;
            foreach ($viikonpaivat as $lyh => $pv): 
                $paivan_pvm = clone $viikon_alku;
                $paivan_pvm->modify("+{$paiva_counter} days");
            ?>
                <th class="day-header"><?= $pv ?><br><small><?= $paivan_pvm->format('d.m') ?></small></th>
            <?php 
                $paiva_counter++;
            endforeach; 
            ?>
        </tr>
    </thead>
    <tbody>
        <?php for ($h = 8; $h <= 17; $h++): ?>
            <tr>
                <th class="time-header"><?= sprintf('%02d:00', $h) ?></th>
                <?php foreach ($viikonpaivat as $lyh => $pv): ?>
                    <td>
                        <?php 
                        $cell_sessiot = [];
                        foreach ($sessiot as $sessio) {
                            if ($sessio['viikonpaiva'] === $lyh &&
                                $sessio['aloitus'] <= $h &&
                                $sessio['lopetus'] > $h &&
                                onkoKurssiKaynnissa($sessio['alkupäivä'], $sessio['loppupäivä'], $viikon_alku, $viikon_loppu) &&
                                $sessio['aloitus'] == $h) {
                                $cell_sessiot[] = $sessio;
                            }
                        }

                        $maara = count($cell_sessiot);
                        if ($maara > 0) {
                            foreach ($cell_sessiot as $index => $sessio) {
                                $kesto = $sessio['lopetus'] - $sessio['aloitus'];
                                $leveys = (100 / $maara) - 2; // -2% pieni väli
                                $left = $index * (100 / $maara);
                        ?>
                            <div class="teacher-session-block" style="
                                top: 2px;
                                left: <?= $left ?>%;
                                width: <?= $leveys ?>%;
                                height: <?= ($kesto * 60) - 8 ?>px;
                            ">
                                <div class="teacher-session-title"><?= htmlspecialchars($sessio['kurssi_nimi']) ?></div>
                                <div class="teacher-session-time"><?= $sessio['aloitus'] ?>:00-<?= $sessio['lopetus'] ?>:00</div>
                                <div class="teacher-session-room">Tila: <?= htmlspecialchars($sessio['tila_nimi']) ?></div>
                            </div>
                        <?php
                            }
                        }
                        ?>
                        </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<div class="viikkonakyma-linkit">
    <a href="lisaa_viikkonakymaan.php?opettajat=<?= $opettaja_id ?>" class="btn">« Muokkaa aikataulua</a>
    <a href="nayta.php?id=<?= $opettaja_id ?>" class="btn">« Takaisin opettajaan</a>
</div>

<?php renderFooter(); ?>
