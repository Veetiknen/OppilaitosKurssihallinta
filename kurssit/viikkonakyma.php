<?php
require '../yhteys.php';
require '../template.php';

if (!isset($_GET['kurssi'])) {
    die("Kurssia ei valittu");
}

$kurssi_id = (int)$_GET['kurssi'];
$viikko = $_GET['viikko'] ?? date('Y-W');

// Haetaan kurssin tiedot
$sql = "SELECT nimi, alkupäivä, loppupäivä, id FROM kurssit WHERE id = ?";
$kysely = $yhteys->prepare($sql);
$kysely->execute([$kurssi_id]);
$kurssi = $kysely->fetch();
if (!$kurssi) {
    die("Kurssia ei löytynyt");
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

// Haetaan kurssin sessiot
$sql = "SELECT s.viikonpaiva, s.aloitus, s.lopetus,
               t.nimi AS tila_nimi,
               o.etunimi AS opettaja_etunimi,
               o.sukunimi AS opettaja_sukunimi
        FROM kurssisessiot s
        JOIN kurssit k ON s.kurssi_id = k.id
        JOIN tilat t ON k.tila = t.id
        JOIN opettajat o ON k.opettaja = o.tunnusnumero
        WHERE s.kurssi_id = ?";
$kysely = $yhteys->prepare($sql);
$kysely->execute([$kurssi_id]);
$sessiot = $kysely->fetchAll(PDO::FETCH_ASSOC);

$viikonpaivat = ['ma'=>'Ma', 'ti'=>'Ti', 'ke'=>'Ke', 'to'=>'To', 'pe'=>'Pe'];

function onkoKurssiKaynnissa($alkupaiva, $loppupaiva, $viikon_alku, $viikon_loppu) {
    $kurssi_alku = new DateTime($alkupaiva);
    $kurssi_loppu = new DateTime($loppupaiva);
    
    return !($kurssi_loppu < $viikon_alku || $kurssi_alku > $viikon_loppu);
}

renderHeader("Viikkonäkymä - " . htmlspecialchars($kurssi['nimi']));
?>

<p><strong>Ajanjakso:</strong> <?= htmlspecialchars($kurssi['alkupäivä']) ?> - <?= htmlspecialchars($kurssi['loppupäivä']) ?></p>
<p><strong>Viikko:</strong> <?= $viikkonro ?>/<?= $vuosi ?> (<?= $viikon_alku->format('d.m.Y') ?> - <?= $viikon_loppu->format('d.m.Y') ?>)</p>

<div class="week-navigation">
    <a href="?kurssi=<?= $kurssi_id ?>&viikko=<?= $edellinen_viikko->format('Y-W') ?>" class="btn">&laquo; Edellinen viikko</a>
    <a href="?kurssi=<?= $kurssi_id ?>&viikko=<?= date('Y-W') ?>" class="btn">Tämä viikko</a>
    <a href="?kurssi=<?= $kurssi_id ?>&viikko=<?= $seuraava_viikko->format('Y-W') ?>" class="btn">Seuraava viikko &raquo;</a>
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
                <th class="time-header">
                    <?= sprintf('%02d:00', $h) ?>
                </th>
                <?php foreach ($viikonpaivat as $lyh => $pv): ?>
                    <td>
                        <?php 
                        foreach ($sessiot as $s) {
                            if ($s['viikonpaiva'] === $lyh && 
                                $s['aloitus'] <= $h && 
                                $s['lopetus'] > $h &&
                                onkoKurssiKaynnissa($kurssi['alkupäivä'], $kurssi['loppupäivä'], $viikon_alku, $viikon_loppu)) {
                                $kesto = $s['lopetus'] - $s['aloitus'];
                                $alkaa_tassa = $s['aloitus'] == $h;
                                
                                if ($alkaa_tassa):
                        ?>
                            <div class="session-block" style="height: <?= ($kesto * 60) - 8 ?>px;">
                            <div class="session-title">Opetus</div>
                            <div class="session-time"><?= $s['aloitus'] ?>:00-<?= $s['lopetus'] ?>:00</div>
                            <div class="session-room">
                                Opettaja: <?= htmlspecialchars($s['opettaja_etunimi'] . " " . $s['opettaja_sukunimi']) ?><br>
                                Tila: <?= htmlspecialchars($s['tila_nimi']) ?>
                            </div>
                        </div>

                        <?php 
                                endif;
                            }
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<a href="lisaa_viikkonakymaan.php?kurssi=<?= $kurssi['id'] ?>" class="btn back-link">Muokkaa aikataulua</a>
<a href="nayta.php?id=<?= $kurssi_id ?>" class="btn back-link">Takaisin kurssiin</a>

<?php renderFooter(); ?>