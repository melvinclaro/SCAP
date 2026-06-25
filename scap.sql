-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-06-2026 a las 05:30:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `scap`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL DEFAULT '',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pregunta_1` varchar(255) DEFAULT NULL,
  `respuesta_1` varchar(255) DEFAULT NULL,
  `pregunta_2` varchar(255) DEFAULT NULL,
  `respuesta_2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `nombre`, `apellido`, `activo`, `created_at`, `updated_at`, `pregunta_1`, `respuesta_1`, `pregunta_2`, `respuesta_2`) VALUES
(1, 'admin', '$2y$10$ieZUROsfzfFGSpNhwi.0COFTJOOC/5gzPsD8WJz0QVtP639Y3/2zS', 'Administrador', 'Principal', 1, '2026-06-09 01:47:12', '2026-06-09 01:47:12', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attendance`
--

CREATE TABLE `attendance` (
  `id` int(10) UNSIGNED NOT NULL,
  `worker_id` int(10) UNSIGNED NOT NULL,
  `fecha` date NOT NULL,
  `hora_entrada` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justifications`
--

CREATE TABLE `justifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `worker_id` int(10) UNSIGNED NOT NULL,
  `motivo` text NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `justifications`
--

INSERT INTO `justifications` (`id`, `worker_id`, `motivo`, `fecha`, `estado`, `created_at`, `updated_at`) VALUES
(1, 9, 'gripe', '2026-06-20', 'Aprobado', '2026-06-20 23:03:52', '2026-06-20 23:03:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workers`
--

CREATE TABLE `workers` (
  `id` int(10) UNSIGNED NOT NULL,
  `cedula` varchar(15) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `cargo` varchar(100) NOT NULL DEFAULT 'Obrero',
  `departamento` varchar(100) NOT NULL DEFAULT 'General',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `workers`
--

INSERT INTO `workers` (`id`, `cedula`, `nombre`, `apellido`, `cargo`, `departamento`, `activo`, `deleted`, `created_at`, `updated_at`) VALUES
(1, '12345678', 'Carlos', 'Rodríguez', 'Electricista', 'Mantenimiento', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(2, '23456789', 'María', 'González', 'Pintora', 'Obras Civiles', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(3, '34567890', 'José', 'Martínez', 'Plomero', 'Mantenimiento', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(4, '45678901', 'Ana', 'López', 'Albañil', 'Obras Civiles', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(5, '56789012', 'Pedro', 'Ramírez', 'Carpintero', 'Taller', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(6, '67890123', 'Luisa', 'Torres', 'Obrera General', 'General', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(7, '78901234', 'Miguel', 'Hernández', 'Soldador', 'Taller', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(8, '89012345', 'Carmen', 'Flores', 'Jardinera', 'Áreas Verdes', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(9, '90123456', 'Roberto', 'Díaz', 'Conductor', 'Transporte', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(10, '01234567', 'Yolanda', 'Pérez', 'Limpieza', 'Servicios', 1, 0, '2026-06-09 01:47:12', '2026-06-09 01:47:12'),
(11, '54545454', 'pres', 'si', 'Obrero', 'General', 1, 0, '2026-06-20 23:02:29', '2026-06-20 23:02:46');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_username` (`username`);

--
-- Indices de la tabla `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_worker_fecha` (`worker_id`,`fecha`);

--
-- Indices de la tabla `justifications`
--
ALTER TABLE `justifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_justifications_worker` (`worker_id`);

--
-- Indices de la tabla `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_cedula` (`cedula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `justifications`
--
ALTER TABLE `justifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `workers`
--
ALTER TABLE `workers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_worker` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `justifications`
--
ALTER TABLE `justifications`
  ADD CONSTRAINT `fk_justifications_worker` FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
