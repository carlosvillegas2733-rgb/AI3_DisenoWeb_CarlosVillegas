<?php

declare(strict_types=1);

/** @var array<int, array<string, mixed>> $estudiantes */
/** @var string $busqueda */
/** @var int $total */

$visibles = count($estudiantes);
$carreras = count(array_unique(array_column($estudiantes, 'carrera')));
?>
<section class="page-heading">
    <div>
        <p class="eyebrow">Panel académico</p>
        <h1>Estudiantes registrados</h1>
        <p class="page-subtitle">
            Consulta, actualiza y administra la información académica almacenada en la base de datos.
        </p>
    </div>

    <a class="button button--primary" href="index.php?accion=crear">
        <span aria-hidden="true">+</span> Registrar estudiante
    </a>
</section>

<section class="stats-grid" aria-label="Resumen del registro">
    <article class="stat-card">
        <span class="stat-label">Total registrado</span>
        <strong class="stat-value"><?= e((string) $total) ?></strong>
        <span class="stat-hint">estudiantes en la base de datos</span>
    </article>

    <article class="stat-card">
        <span class="stat-label">Resultados visibles</span>
        <strong class="stat-value"><?= e((string) $visibles) ?></strong>
        <span class="stat-hint"><?= $busqueda !== '' ? 'coinciden con la búsqueda' : 'sin filtros aplicados' ?></span>
    </article>

    <article class="stat-card stat-card--accent">
        <span class="stat-label">Carreras</span>
        <strong class="stat-value"><?= e((string) $carreras) ?></strong>
        <span class="stat-hint">programas representados</span>
    </article>
</section>

<section class="card">
    <div class="card-toolbar">
        <form class="search-form" method="get" action="index.php" role="search">
            <input type="hidden" name="accion" value="listar">
            <label class="sr-only" for="buscar">Buscar estudiante</label>
            <input
                id="buscar"
                name="buscar"
                type="search"
                value="<?= e($busqueda) ?>"
                placeholder="Buscar por cédula, nombre, correo o carrera"
                autocomplete="off"
            >
            <button class="button button--secondary" type="submit">Buscar</button>
            <?php if ($busqueda !== ''): ?>
                <a class="button button--ghost" href="index.php">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($estudiantes === []): ?>
        <div class="empty-state">
            <h2>No hay estudiantes para mostrar</h2>
            <p>
                <?= $busqueda !== ''
                    ? 'No se encontraron coincidencias con tu búsqueda.'
                    : 'Registra el primer estudiante para comenzar.' ?>
            </p>
            <a class="button button--primary" href="index.php?accion=crear">Registrar estudiante</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Cédula</th>
                        <th scope="col">Estudiante</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Carrera</th>
                        <th scope="col">Semestre</th>
                        <th scope="col">Registro</th>
                        <th scope="col" class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td data-label="Cédula">
                            <span class="mono"><?= e($estudiante['cedula']) ?></span>
                        </td>
                        <td data-label="Estudiante">
                            <strong><?= e($estudiante['apellidos']) ?>, <?= e($estudiante['nombres']) ?></strong>
                            <small class="muted">Tel. <?= e($estudiante['telefono']) ?></small>
                        </td>
                        <td data-label="Correo">
                            <a href="mailto:<?= e($estudiante['correo']) ?>"><?= e($estudiante['correo']) ?></a>
                        </td>
                        <td data-label="Carrera"><?= e($estudiante['carrera']) ?></td>
                        <td data-label="Semestre">
                            <span class="badge"><?= e((string) $estudiante['semestre']) ?>°</span>
                        </td>
                        <td data-label="Registro">
                            <time datetime="<?= e((string) $estudiante['fecha_creacion']) ?>">
                                <?= e(date('d/m/Y', strtotime((string) $estudiante['fecha_creacion']))) ?>
                            </time>
                        </td>
                        <td data-label="Acciones" class="actions">
                            <a
                                class="button button--small button--secondary"
                                href="index.php?accion=editar&amp;id=<?= e((string) $estudiante['id']) ?>"
                            >Editar</a>

                            <form
                                method="post"
                                action="index.php?accion=eliminar"
                                class="inline-form"
                                data-confirm="¿Eliminar a <?= e($estudiante['nombres'] . ' ' . $estudiante['apellidos']) ?>? Esta acción no se puede deshacer."
                            >
                                <?= \App\Core\Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= e((string) $estudiante['id']) ?>">
                                <button class="button button--small button--danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
