-- ============================================================================
-- Actividad Integradora 3 - Sistema Web de Gestion de Estudiantes
-- Engine: MySQL 8.x / MariaDB 10.4+
-- Charset: utf8mb4
-- ============================================================================

CREATE DATABASE IF NOT EXISTS db_sistema_web
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_sistema_web;

DROP TABLE IF EXISTS estudiantes;

CREATE TABLE estudiantes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cedula CHAR(10) NOT NULL,
    nombres VARCHAR(80) NOT NULL,
    apellidos VARCHAR(80) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    carrera VARCHAR(80) NOT NULL,
    semestre TINYINT UNSIGNED NOT NULL,
    telefono VARCHAR(16) NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_estudiantes_cedula (cedula),
    UNIQUE KEY uq_estudiantes_correo (correo),
    KEY idx_estudiantes_apellidos (apellidos),
    KEY idx_estudiantes_carrera (carrera),
    CONSTRAINT chk_estudiantes_semestre CHECK (semestre BETWEEN 1 AND 8)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO estudiantes (cedula, nombres, apellidos, correo, carrera, semestre, telefono, fecha_creacion)
VALUES
    ('1133468080', 'Andrea Carolina', 'Mendoza Vera', 'andrea.mendoza@ecotec.edu.ec', 'Ingeniería en Sistemas Inteligentes', 5, '0991234567', DATE_SUB(NOW(), INTERVAL 45 DAY)),
    ('3056161056', 'Luis Fernando', 'Andrade Salazar', 'luis.andrade@ecotec.edu.ec', 'Ingeniería en Redes Inteligentes y Ciberseguridad', 3, '0987654321', DATE_SUB(NOW(), INTERVAL 38 DAY)),
    ('1056714569', 'María José', 'Peña O''Connor', 'maria.pena@ecotec.edu.ec', 'Ingeniería en Sistemas Inteligentes', 7, '0998765432', DATE_SUB(NOW(), INTERVAL 31 DAY)),
    ('0139536676', 'Daniel Alejandro', 'Cruz Medina', 'daniel.cruz@ecotec.edu.ec', 'Ingeniería en Mecatrónica', 4, '0976543210', DATE_SUB(NOW(), INTERVAL 25 DAY)),
    ('2110671779', 'Sofía Isabel', 'Ramírez Torres', 'sofia.ramirez@ecotec.edu.ec', 'Ingeniería Industrial', 2, '0965432109', DATE_SUB(NOW(), INTERVAL 19 DAY)),
    ('2331868113', 'Jorge Andrés', 'Villacís Moreira', 'jorge.villacis@ecotec.edu.ec', 'Ingeniería Civil', 6, '0954321098', DATE_SUB(NOW(), INTERVAL 14 DAY)),
    ('2155047208', 'Gabriela Alejandra', 'Zambrano Ruiz', 'gabriela.zambrano@ecotec.edu.ec', 'Arquitectura', 8, '0943210987', DATE_SUB(NOW(), INTERVAL 10 DAY)),
    ('0946146305', 'Ricardo Javier', 'Baquerizo León', 'ricardo.baquerizo@ecotec.edu.ec', 'Ingeniería en Sistemas Inteligentes', 1, '0932109876', DATE_SUB(NOW(), INTERVAL 6 DAY)),
    ('0148512478', 'Valentina Nicole', 'Espinoza Cedeño', 'valentina.espinoza@ecotec.edu.ec', 'Ingeniería en Redes Inteligentes y Ciberseguridad', 5, '0921098765', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    ('1118751765', 'Mateo Sebastián', 'Jaramillo Paredes', 'mateo.jaramillo@ecotec.edu.ec', 'Ingeniería en Mecatrónica', 3, '0910987654', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Verification queries
SELECT COUNT(*) AS total_estudiantes FROM estudiantes;
SELECT id, cedula, nombres, apellidos, carrera, semestre, fecha_creacion
FROM estudiantes
ORDER BY id;
