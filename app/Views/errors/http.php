<?php

declare(strict_types=1);

/** @var int $codigo */
/** @var string $mensaje */

$tituloError = match ($codigo) {
    404 => 'Página no encontrada',
    405 => 'Método no permitido',
    419 => 'Sesión expirada',
    default => 'No pudimos completar la solicitud',
};
?>
<section class="error-page">
    <p class="error-code"><?= e((string) $codigo) ?></p>
    <h1><?= e($tituloError) ?></h1>
    <p><?= e($mensaje) ?></p>
    <a class="button button--primary" href="index.php">Volver al listado</a>
</section>
