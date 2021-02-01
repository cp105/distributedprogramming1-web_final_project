-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Giu 18, 2019 alle 15:14
-- Versione del server: 10.1.40-MariaDB
-- Versione PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `s257443`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `airplane_seats`
--

DROP TABLE IF EXISTS `airplane_seats`;
CREATE TABLE `airplane_seats` (
  `seat_number` int(11) NOT NULL,
  `status` varchar(36) NOT NULL,
  `email` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `airplane_seats`
--

INSERT INTO `airplane_seats` (`seat_number`, `status`, `email`) VALUES
(0, 'free', ''),
(1, 'free', ''),
(2, 'free', ''),
(3, 'free', ''),
(4, 'free', ''),
(5, 'free', ''),
(6, 'free', ''),
(7, 'free', ''),
(8, 'free', ''),
(9, 'free', ''),
(10, 'free', ''),
(11, 'free', ''),
(12, 'free', ''),
(13, 'free', ''),
(14, 'free', ''),
(15, 'free', ''),
(16, 'free', ''),
(17, 'free', ''),
(18, 'free', ''),
(19, 'free', ''),
(20, 'free', ''),
(21, 'free', ''),
(22, 'free', ''),
(23, 'free', ''),
(24, 'free', ''),
(25, 'free', ''),
(26, 'free', ''),
(27, 'free', ''),
(28, 'free', ''),
(29, 'free', ''),
(30, 'free', ''),
(31, 'free', ''),
(32, 'free', ''),
(33, 'free', ''),
(34, 'free', ''),
(35, 'free', ''),
(36, 'free', ''),
(37, 'free', ''),
(38, 'free', ''),
(39, 'free', ''),
(40, 'free', ''),
(41, 'free', ''),
(42, 'free', ''),
(43, 'free', ''),
(44, 'free', ''),
(45, 'free', ''),
(46, 'free', ''),
(47, 'free', ''),
(48, 'free', ''),
(49, 'free', ''),
(50, 'free', ''),
(51, 'free', ''),
(52, 'free', ''),
(53, 'free', ''),
(54, 'free', ''),
(55, 'free', ''),
(56, 'free', ''),
(57, 'free', ''),
(58, 'free', ''),
(59, 'free', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `email` varchar(36) NOT NULL,
  `password` char(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`email`, `password`) VALUES
('u1@p.it', '2439a64be91a99151f6cf065c422d3a4253dd9d3684a5baf1552d35ab93ed348b5093df547202117310369867474a7b8aa4452b6fd278ec342204eaddfe01888'),
('u2@p.it', '6352f1fcc8610fde1d8eb5a3f46dd9daea28c5d46b268b11d2725ff965dfdb70a1d737d21bd61d1f93c4c811a4fdb57d6208e4a1a6c79e986ca54b85f56087a0');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `airplane_seats`
--
ALTER TABLE `airplane_seats`
  ADD PRIMARY KEY (`seat_number`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
