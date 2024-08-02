-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-02-2021 a las 18:21:32
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
-- Estructura de tabla para la tabla `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `grado` varchar(50) NOT NULL,
  `abv` varchar(50) NOT NULL,
  `producto_id` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `grades`
--

INSERT INTO `grades` (`id`, `grado`, `abv`, `producto_id`) VALUES
(1, '100', '100', 3),
(2, '090', '090', 3),
(3, '080', '080', 3),
(4, '070', '070', 3),
(5, '060', '060', 3),
(6, '050', '050', 3),
(7, '040', '040', 3),
(8, '030', '030', 3),
(9, 'SELECT', 'SEL', 1),
(10, 'FANCY', 'FAN', 1),
(11, 'STANDARD', 'STD', 1),
(12, 'SHORT', 'SHR', 1),
(13, 'NACIONAL', 'NAL', 3),
(14, 'NACIONAL', 'NAL', 1),
(15, '100', '100', 4),
(16, '090', '090', 4),
(17, '080', '080', 4),
(18, '070', '070', 4),
(19, '060', '060', 4),
(20, '050', '050', 4),
(21, '040', '040', 4),
(22, '030', '030', 4),
(23, 'NACIONAL', 'NAL', 4),
(30, 'SELECT', 'SEL', 2),
(31, 'FANCY', 'FAN', 2),
(32, 'STANDARD', 'STD', 2),
(33, 'SHORT', 'SHR', 2),
(34, 'NACIONAL', 'NAL', 2),
(37, 'SELECT', 'SEL', 6),
(38, 'FANCY', 'FAN', 6),
(39, 'STANDARD', 'STD', 6),
(40, 'SHORT', 'SHR', 6),
(41, 'NACIONAL', 'NAL', 6),
(44, 'SELECT', 'SEL', 5),
(45, 'FANCY', 'FAN', 5),
(46, 'STANDARD', 'STD', 5),
(47, 'SHORT', 'SHR', 5),
(48, 'NACIONAL', 'NAL', 5);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_products_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
