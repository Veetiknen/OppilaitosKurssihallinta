-- Combined Database Schema
-- Merged from veeti_koistinen.sql and testikanta.sql
-- Generation Time: Aug 28, 2025

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
(1, 'Matti', 'Meikäläinen', '2005-02-15', 1),
(2, 'Romy', 'Shuttleworth', '1964-10-11', 1),
(3, 'Franzen', 'Rosenstengel', '1976-10-16', 2),
(4, 'Engelbert', 'Sheerman', '1985-06-16', 3),
(5, 'Cameron', 'Le Borgne', '1981-10-29', 4);

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
(1, 'Huone1', 30),
(2, 'Auditorium', 100),
(3, 'Library', 25),
(4, 'Room 101', 20);

-- --------------------------------------------------------

--
-- Table structure for table `kurssit`
--

CREATE TABLE `kurssit` (
  `id` int(11) NOT NULL,
  `nimi` varchar(30) NOT NULL,
  `kuvaus` varchar(30) NOT NULL,
  `alkupäivä` date NOT NULL,
  `loppupäivä` date NOT NULL,
  `opettaja` int(30) NOT NULL,
  `tila` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurssit`
--

INSERT INTO `kurssit` (`id`, `nimi`, `kuvaus`, `alkupäivä`, `loppupäivä`, `opettaja`, `tila`) VALUES
(1, 'Testi', 'Testikurssi', '2025-09-21', '2026-01-06', 0, 1),
(2, 'Biology 101', 'Introduction to Biology', '2025-09-01', '2025-12-15', 1, 2),
(3, 'Physical Education', 'Basic PE Course', '2025-09-01', '2025-12-15', 2, 4),
(4, 'Advanced Science', 'Advanced Science Topics', '2025-09-15', '2026-01-20', 3, 3),
(5, 'English Literature', 'Modern English Literature', '2025-09-10', '2025-12-20', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kurssikirjautumisilla`
--

CREATE TABLE `kurssikirjautumisilla` (
  `id` int(11) NOT NULL,
  `opiskelija` int(30) NOT NULL,
  `kurssi` int(30) NOT NULL,
  `Kirjautumispäivä` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kurssikirjautumisilla`
--

INSERT INTO `kurssikirjautumisilla` (`id`, `opiskelija`, `kurssi`, `Kirjautumispäivä`) VALUES
(1, 1, 1, '2025-08-28 07:48:47'),
(2, 2, 2, '2025-08-28 08:00:00'),
(3, 3, 3, '2025-08-28 08:15:00'),
(4, 4, 4, '2025-08-28 08:30:00'),
(5, 5, 5, '2025-08-28 08:45:00');

--
-- Indexes for dumped tables
--

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

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;