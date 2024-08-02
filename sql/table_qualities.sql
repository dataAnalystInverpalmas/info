-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2021 a las 18:22:35
-- Versión del servidor: 10.4.14-MariaDB
-- Versión de PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `informes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `table_qualities`
--

CREATE TABLE `table_qualities` (
  `id` int(11) NOT NULL,
  `finca` int(11) NOT NULL,
  `variedad` int(11) DEFAULT NULL,
  `fuente` tinyint(4) NOT NULL,
  `grado` tinyint(4) NOT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `valor` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `table_qualities`
--

INSERT INTO `table_qualities` (`id`, `finca`, `variedad`, `fuente`, `grado`, `fecha`, `valor`, `fecha_registro`) VALUES
(9, 2, 9, 3, 9, '2021-01-27', 1, '2021-01-27 13:29:24'),
(10, 2, 9, 3, 9, '2021-01-27', 1, '2021-01-27 13:30:13'),
(14, 2, 9, 3, 14, '2021-01-27', 1, '2021-01-27 13:43:33'),
(17, 2, 9, 3, 14, '2021-01-27', 10, '2021-01-27 14:56:03'),
(18, 2, 9, 3, 9, '2021-01-27', 10, '2021-01-27 15:05:46'),
(19, 2, 9, 3, 10, '2021-01-27', 10, '2021-01-27 15:06:07'),
(20, 2, 9, 3, 11, '2021-01-27', 1, '2021-01-27 15:06:12'),
(21, 2, 9, 3, 9, '2021-01-27', 10, '2021-01-27 15:08:43'),
(22, 2, 9, 3, 10, '2021-01-27', 10, '2021-01-27 15:08:49'),
(23, 2, 9, 3, 14, '2021-01-27', 10, '2021-01-27 15:08:55'),
(24, 2, 41, 3, 10, '2021-01-30', 10, '2021-01-30 08:01:08');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `table_qualities`
--
ALTER TABLE `table_qualities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `table_qualities`
--
ALTER TABLE `table_qualities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
