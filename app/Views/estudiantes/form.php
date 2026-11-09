<?php

declare(strict_types=1);

/** @var array<string, mixed> $estudiante */
/** @var array<string, string> $errores */
/** @var string $accion */
/** @var bool $esEdicion */
/** @var array<int, string> $carreras */

$valor = static function (string $campo) use ($estudiante): string {
    return e((string) ($estudiante[$campo] ?? ''));
};

$error = static function (string $campo) use ($errores): ?string {
    return $errores[$campo] ?? null;
};
?>
<section class="page-heading page-heading--compact">
    <div>
        <p class="eyebrow"><?= $esEdicion ? 'Actualización de datos' : 'Nuevo registro' ?></p>
        <h1><?= $esEdicion ? 'Editar estudiante' : 'Registrar estudiante' ?></h1>
        <p class="page-subtitle">
            Los campos marcados con <span class="required">*</span> son obligatorios.
        </p>
    </div>

    <a class="button button--ghost" href="index.php">Volver al listado</a>
</section>

<?php if ($errores !== []): ?>
    <div class="alert alert--error" role="alert">
        <span class="alert-icon" aria-hidden="true">!</span>
        <p>Revisa los campos marcados. Encontramos <?= count($errores) ?> error(es) de validación.</p>
    </div>
<?php endif; ?>

<section class="card form-card">
    <form
        method="post"
        action="index.php?accion=<?= e($accion) ?>"
        novalidate
        data-validate-form
    >
        <?= \App\Core\Csrf::field() ?>

        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= $valor('id') ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="field">
                <label for="cedula">Cédula <span class="required">*</span></label>
                <input
                    id="cedula"
                    name="cedula"
                    type="text"
                    inputmode="numeric"
                    maxlength="10"
                    pattern="\d{10}"
                    value="<?= $valor('cedula') ?>"
                    required
                    autocomplete="off"
                    aria-describedby="cedula-help<?= $error('cedula') !== null ? ' cedula-error' : '' ?>"
                    <?= $error('cedula') !== null ? 'aria-invalid="true"' : '' ?>
                >
                <small id="cedula-help" class="field-help">10 dígitos, sin espacios ni guiones.</small>
                <?php if ($error('cedula') !== null): ?>
                    <p id="cedula-error" class="field-error"><?= e($error('cedula')) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="telefono">Teléfono <span class="required">*</span></label>
                <input
                    id="telefono"
                    name="telefono"
                    type="tel"
                    maxlength="16"
                    pattern="\+?\d{7,15}"
                    value="<?= $valor('telefono') ?>"
                    required
                    autocomplete="tel"
                    aria-describedby="telefono-help<?= $error('telefono') !== null ? ' telefono-error' : '' ?>"
                    <?= $error('telefono') !== null ? 'aria-invalid="true"' : '' ?>
                >
                <small id="telefono-help" class="field-help">Entre 7 y 15 dígitos; puede iniciar con +.</small>
                <?php if ($error('telefono') !== null): ?>
                    <p id="telefono-error" class="field-error"><?= e($error('telefono')) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="nombres">Nombres <span class="required">*</span></label>
                <input
                    id="nombres"
                    name="nombres"
                    type="text"
                    maxlength="80"
                    minlength="2"
                    value="<?= $valor('nombres') ?>"
                    required
                    autocomplete="given-name"
                    <?= $error('nombres') !== null ? 'aria-invalid="true"' : '' ?>
                >
                <?php if ($error('nombres') !== null): ?>
                    <p class="field-error"><?= e($error('nombres')) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="apellidos">Apellidos <span class="required">*</span></label>
                <input
                    id="apellidos"
                    name="apellidos"
                    type="text"
                    maxlength="80"
                    minlength="2"
                    value="<?= $valor('apellidos') ?>"
                    required
                    autocomplete="family-name"
                    <?= $error('apellidos') !== null ? 'aria-invalid="true"' : '' ?>
                >
                <?php if ($error('apellidos') !== null): ?>
                    <p class="field-error"><?= e($error('apellidos')) ?></p>
                <?php endif; ?>
            </div>

            <div class="field field--full">
                <label for="correo">Correo electrónico <span class="required">*</span></label>
                <input
                    id="correo"
                    name="correo"
                    type="email"
                    maxlength="120"
                    value="<?= $valor('correo') ?>"
                    required
                    autocomplete="email"
                    <?= $error('correo') !== null ? 'aria-invalid="true"' : '' ?>
                >
                <?php if ($error('correo') !== null): ?>
                    <p class="field-error"><?= e($error('correo')) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="carrera">Carrera <span class="required">*</span></label>
                <select id="carrera" name="carrera" required <?= $error('carrera') !== null ? 'aria-invalid="true"' : '' ?>>
                    <option value="">Selecciona una carrera</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <option value="<?= e($carrera) ?>" <?= $valor('carrera') === e($carrera) ? 'selected' : '' ?>>
                            <?= e($carrera) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($error('carrera') !== null): ?>
                    <p class="field-error"><?= e($error('carrera')) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="semestre">Semestre <span class="required">*</span></label>
                <select id="semestre" name="semestre" required <?= $error('semestre') !== null ? 'aria-invalid="true"' : '' ?>>
                    <option value="">Selecciona un semestre</option>
                    <?php for ($semestre = 1; $semestre <= 8; $semestre++): ?>
                        <option value="<?= $semestre ?>" <?= $valor('semestre') === (string) $semestre ? 'selected' : '' ?>>
                            <?= $semestre ?>°
                        </option>
                    <?php endfor; ?>
                </select>
                <?php if ($error('semestre') !== null): ?>
                    <p class="field-error"><?= e($error('semestre')) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-note">
            <strong>Validación de seguridad:</strong> la cédula se verifica con su dígito verificador y todos los
            datos se almacenan mediante sentencias preparadas.
        </div>

        <div class="form-actions">
            <button class="button button--primary" type="submit">
                <?= $esEdicion ? 'Actualizar estudiante' : 'Guardar estudiante' ?>
            </button>
            <a class="button button--ghost" href="index.php">Cancelar</a>
        </div>
    </form>
</section>
