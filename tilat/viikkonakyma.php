<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['tilat'])) {
    die("Tilaa ei valittu");
}

$tila_id = (int)$_GET['tilat'];
$viikko = $_GET['viikko'] ?? date('Y-W');

// Haetaan tilan tiedot
$sql = "SELECT id, nimi, kapasiteetti FROM tilat WHERE id = ?";
$kysely = $yhteys->prepare($sql);
$kysely->execute([$tila_id]);
$tila = $kysely->fetch(PDO::FETCH_ASSOC);
if (!$tila) {
    die("Tilaa ei löytynyt");
}

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

// Haetaan kaikki tämän tilan kurssit ja niiden sessiot
$sql = "SELECT s.viikonpaiva, s.aloitus, s.lopetus,
               k.nimi AS kurssi_nimi, k.alkupäivä, k.loppupäivä,
               o.etunimi AS op_etunimi, o.sukunimi AS op_sukunimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        JOIN opettajat o ON k.opettaja = o.tunnusnumero
        WHERE k.tila = ?";
$kysely = $yhteys->prepare($sql);
$kysely->execute([$tila_id]);
$sessiot = $kysely->fetchAll(PDO::FETCH_ASSOC);

$viikonpaivat = ['ma'=>'Ma', 'ti'=>'Ti', 'ke'=>'Ke', 'to'=>'To', 'pe'=>'Pe'];

function onkoKurssiKaynnissa($alkupaiva, $loppupaiva, $viikon_alku, $viikon_loppu) {
    $kurssi_alku = new DateTime($alkupaiva);
    $kurssi_loppu = new DateTime($loppupaiva);
    return !($kurssi_loppu < $viikon_alku || $kurssi_alku > $viikon_loppu);
}

renderHeader("Viikkonäkymä - Tila: " . htmlspecialchars($tila['nimi']));
?>

<p><strong>Tila:</strong> <?= htmlspecialchars($tila['nimi']) ?> (kapasiteetti: <?= (int)$tila['kapasiteetti'] ?>)</p>
<p><strong>Viikko:</strong> <?= $viikkonro ?>/<?= $vuosi ?> (<?= $viikon_alku->format('d.m.Y') ?> - <?= $viikon_loppu->format('d.m.Y') ?>)</p>

<div class="week-navigation">
    <a href="?tilat=<?= $tila_id ?>&viikko=<?= $edellinen_viikko->format('Y-W') ?>" class="btn">&laquo; Edellinen viikko</a>
    <a href="?tilat=<?= $tila_id ?>&viikko=<?= date('Y-W') ?>" class="btn">Tämä viikko</a>
    <a href="?tilat=<?= $tila_id ?>&viikko=<?= $seuraava_viikko->format('Y-W') ?>" class="btn">Seuraava viikko &raquo;</a>
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
                <th class="day-header">
                    <?= $pv ?><br><small><?= $paivan_pvm->format('d.m') ?></small>
                </th>
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
                        foreach ($sessiot as $s) {
                            if ($s['viikonpaiva'] === $lyh && 
                                $s['aloitus'] <= $h && 
                                $s['lopetus'] > $h &&
                                onkoKurssiKaynnissa($s['alkupäivä'], $s['loppupäivä'], $viikon_alku, $viikon_loppu) &&
                                $s['aloitus'] == $h) {
                                $cell_sessiot[] = $s;
                            }
                        }

                        $maara = count($cell_sessiot);
                        if ($maara > 0):
                            $index = 0;
                            foreach ($cell_sessiot as $s):
                                $kesto = $s['lopetus'] - $s['aloitus'];
                                $leveys = (100 / $maara) - 2;
                                $left = $index * (100 / $maara);
                        ?>
                            <div class="room-session-block" style="left: <?= $left ?>%; width: <?= $leveys ?>%; height: <?= ($kesto * 60) - 8 ?>px;">
                                <div class="room-session-title"><?= htmlspecialchars($s['kurssi_nimi']) ?></div>
                                <div class="room-session-time"><?= $s['aloitus'] ?>:00-<?= $s['lopetus'] ?>:00</div>
                                <div class="room-session-teacher">Opettaja: <?= htmlspecialchars($s['op_etunimi'] . ' ' . $s['op_sukunimi']) ?></div>
                            </div>
                        <?php
                                $index++;
                            endforeach;
                        endif;
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<a href="lisaa_viikkonakymaan_tila.php?tilat=<?= $tila_id ?>" class="btn back-link">&laquo; Muokkaa tilan aikataulua</a>
<a href="nayta.php?id=<?= $tila_id ?>" class="btn back-link">&laquo; Takaisin tilaan</a>

<?php renderFooter(); ?>
