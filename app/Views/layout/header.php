<?php

declare(strict_types=1);

/** @var string $titulo */
$titulo = $titulo ?? 'Gestión de Estudiantes';
$flash = \App\Core\Flash::pull();
$flashType = $flash !== null && $flash['type'] === 'success' ? 'success' : 'error';
$flashMessage = $flash['message'] ?? '';
$currentAction = isset($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : 'listar';
$isListing = $currentAction === 'listar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema web para la gestión académica de estudiantes, desarrollado con PHP, PDO y MySQL.">
    <title><?= e($titulo) ?> | Gestión de Estudiantes</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>
<a class="skip-link" href="#contenido">Saltar al contenido principal</a>

<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php" aria-label="Ir al listado de estudiantes">
            <span class="brand-mark" aria-hidden="true">GE</span>
            <span class="brand-text">
                <strong>Gestión de Estudiantes</strong>
                <small>Programación de Sistemas Web · ECOTEC</small>
            </span>
        </a>

        <nav class="main-nav" aria-label="Navegación principal">
            <a
                class="nav-link<?= $isListing ? ' is-active' : '' ?>"
                href="index.php"
                <?= $isListing ? 'aria-current="page"' : '' ?>
            >Estudiantes</a>
            <a class="button button--primary button--small" href="index.php?accion=crear">Nuevo estudiante</a>
        </nav>
    </div>
</header>

<main id="contenido" class="container page">
    <?php if ($flash !== null): ?>
        <div class="alert alert--<?= $flashType ?>" role="status" data-alert>
            <span class="alert-icon" aria-hidden="true"><?= $flashType === 'success' ? '✓' : '!' ?></span>
            <p><?= e($flashMessage) ?></p>
            <button type="button" class="alert-close" aria-label="Cerrar mensaje" data-dismiss-alert>&times;</button>
        </div>
    <?php endif; ?>
