SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Luo tietokanta
CREATE DATABASE IF NOT EXISTS `projekti`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `projekti`;

-- --------------------------------------------------------
-- Table structure for table `opettajat`

CREATE TABLE `opettajat` (
  `tunnusnumero` int(30) NOT NULL,
  `etunimi` varchar(30) NOT NULL,
  `sukunimi` varchar(30) NOT NULL,
  `aine` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opettajat`
--

INSERT INTO `opettajat` (`tunnusnumero`, `etunimi`, `sukunimi`, `aine`) VALUES
(0, 'Tuntematon', 'Opettaja', 'Ei määritelty'),
(1, 'Emmott', 'Fleming', 'Science'),
(2, 'Xymenes', 'Beddow', 'Physical Education'),
(3, 'Cyndia', 'Godlee', 'Science'),
(4, 'Libbi', 'Muggeridge', 'English');

-- --------------------------------------------------------

--
-- Table structure for table `opiskelijat`
--

CREATE TABLE `opiskelijat` (
  `opiskelija_numero` int(11) NOT NULL,
  `etunimi` varchar(30) NOT NULL,
  `sukunimi` varchar(30) NOT NULL,
  `syntymäpäivä` date NOT NULL,
  `vuosikurssi` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opiskelijat`
--

INSERT INTO `opiskelijat` (`opiskelija_numero`, `etunimi`, `sukunimi`, `syntymäpäivä`, `vuosikurssi`) VALUES
(0, 'Malli', 'Oppilas', '2005-02-15', 1),
(1, 'Romy', 'Shuttleworth', '1964-10-11', 1),
(2, 'Franzen', 'Rosenstengel', '1976-10-16', 2),
(3, 'Engelbert', 'Sheerman', '1985-06-16', 3),
(4, 'Cameron', 'Le Borgne', '1981-10-29', 4);

-- --------------------------------------------------------

--
-- Table structure for table `tilat`
--

CREATE TABLE `tilat` (
  `id` int(30) NOT NULL,
  `nimi` varchar(30) NOT NULL,
  `kapasiteetti` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tilat`
--

INSERT INTO `tilat` (`id`, `nimi`, `kapasiteetti`) VALUES
(0, 'Ei valittu', 0),
(1, 'Auditorium', 100),
(2, 'Library', 25),
(3, 'Room 101', 20),
(4, 'Luokka 4', 30);

-- --------------------------------------------------------

--
-- Table structure for table `kurssit`
--

CREATE TABLE `kurssit` (
  `id` int(11) NOT NULL,
  `nimi` varchar(30) NOT NULL,
  `aine` varchar(50) NOT NULL,
  `kuvaus` varchar(30) NOT NULL,
  `alkupäivä` date NOT NULL,
  `loppupäivä` date NOT NULL,
  `opettaja` int(30) NOT NULL,
  `tila` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurssit`
--

INSERT INTO `kurssit` (`id`, `nimi`, `aine`, `kuvaus`, `alkupäivä`, `loppupäivä`, `opettaja`, `tila`) VALUES
(0, 'Ei valittu', 'Ei määritelty', 'Ei kurssia valittu', '2025-01-01', '2025-12-31', 0, 0),
(1, 'Biology 101', 'Science', 'Introduction to Biology', '2025-09-01', '2025-12-15', 1, 2),
(2, 'PE Basics', 'Physical Education', 'Basic PE Course', '2025-09-01', '2025-12-15', 2, 4),
(3, 'Advanced Science', 'Science', 'Advanced Science Topics', '2025-09-15', '2026-01-20', 3, 3),
(4, 'English Literature', 'English', 'Modern English Literature', '2025-09-10', '2025-12-20', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kurssikirjautumisilla`
--

CREATE TABLE `kurssikirjautumisilla` (
  `id` int(11) NOT NULL,
  `opiskelija` int(30) NOT NULL,
  `kurssi` int(30) NOT NULL,
  `kirjautumispäivä` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurssikirjautumisilla`
--

INSERT INTO `kurssikirjautumisilla` (`id`, `opiskelija`, `kurssi`, `kirjautumispäivä`) VALUES
(0, 0, 0, '2025-08-28 07:48:47'),
(1, 1, 1, '2025-08-28 08:00:00'),
(2, 2, 2, '2025-08-28 08:15:00'),
(3, 3, 3, '2025-08-28 08:30:00'),
(4, 4, 4, '2025-08-28 08:45:00');

-- --------------------------------------------------------
-- Table structure for table `kurssisessiot`
-- --------------------------------------------------------

CREATE TABLE `kurssisessiot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kurssi_id` int(11) NOT NULL,
  `viikonpaiva` enum('ma','ti','ke','to','pe') NOT NULL,
  `aloitus` tinyint(4) NOT NULL,
  `lopetus` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kurssi_id` (`kurssi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Poista vanhat sessiot ensin
DELETE FROM kurssisessiot;

-- Huom: aloitus ja lopetus kentät ovat tinyint(4), joten käytetään tunteja (8 = 08:00, 10 = 10:00 jne.)

INSERT INTO `kurssisessiot` (`kurssi_id`, `viikonpaiva`, `aloitus`, `lopetus`) VALUES
-- Biology 101 (kurssi_id = 1)
(1, 'ma', 9, 11),
(1, 'ke', 13, 15),
(1, 'pe', 10, 12),

-- PE Basics (kurssi_id = 2)
(2, 'ti', 8, 10),
(2, 'to', 14, 16),

-- Advanced Science (kurssi_id = 3)
(3, 'ma', 10, 12),
(3, 'ke', 9, 11),
(3, 'pe', 13, 15),

-- English Literature (kurssi_id = 4)
(4, 'ti', 9, 11),
(4, 'to', 11, 13),
(4, 'pe', 14, 16);

--
-- Indexes for table `opettajat`
--
ALTER TABLE `opettajat`
  ADD PRIMARY KEY (`tunnusnumero`);

--
-- Indexes for table `opiskelijat`
--
ALTER TABLE `opiskelijat`
  ADD PRIMARY KEY (`opiskelija_numero`);

--
-- Indexes for table `tilat`
--
ALTER TABLE `tilat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kurssit`
--
ALTER TABLE `kurssit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tila` (`tila`),
  ADD KEY `opettaja` (`opettaja`);

--
-- Indexes for table `kurssikirjautumisilla`
--
ALTER TABLE `kurssikirjautumisilla`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opiskelija` (`opiskelija`),
  ADD KEY `kurssi` (`kurssi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `opettajat`
--
ALTER TABLE `opettajat`
  MODIFY `tunnusnumero` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `opiskelijat`
--
ALTER TABLE `opiskelijat`
  MODIFY `opiskelija_numero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tilat`
--
ALTER TABLE `tilat`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kurssit`
--
ALTER TABLE `kurssit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kurssikirjautumisilla`
--
ALTER TABLE `kurssikirjautumisilla`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kurssisessiot`
--
ALTER TABLE `kurssisessiot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kurssit`
--
ALTER TABLE `kurssit`
  ADD CONSTRAINT `kurssit_ibfk_1` FOREIGN KEY (`tila`) REFERENCES `tilat` (`id`),
  ADD CONSTRAINT `kurssit_ibfk_2` FOREIGN KEY (`opettaja`) REFERENCES `opettajat` (`tunnusnumero`);

--
-- Constraints for table `kurssikirjautumisilla`
--
ALTER TABLE `kurssikirjautumisilla`
  ADD CONSTRAINT `kurssikirjautumisilla_ibfk_1` FOREIGN KEY (`opiskelija`) REFERENCES `opiskelijat` (`opiskelija_numero`),
  ADD CONSTRAINT `kurssikirjautumisilla_ibfk_2` FOREIGN KEY (`kurssi`) REFERENCES `kurssit` (`id`);

--
-- Constraints for table `kurssisessiot`
--
ALTER TABLE `kurssisessiot`
  ADD CONSTRAINT `kurssisessiot_ibfk_1` FOREIGN KEY (`kurssi_id`) REFERENCES `kurssit` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;