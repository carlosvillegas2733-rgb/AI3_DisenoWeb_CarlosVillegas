<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Controllers\EstudianteController;
use App\Core\HttpException;
use App\Core\View;

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

$action = isset($_GET['accion']) && is_string($_GET['accion']) ? $_GET['accion'] : 'listar';

try {
    $controller = new EstudianteController();

    match ($action) {
        'listar' => $controller->index(),
        'crear' => $controller->create(),
        'guardar' => $controller->store(),
        'editar' => $controller->edit(),
        'actualizar' => $controller->update(),
        'eliminar' => $controller->destroy(),
        default => throw new HttpException(404, 'La página solicitada no existe.'),
    };
} catch (HttpException $exception) {
    http_response_code($exception->statusCode);
    View::render('errors/http', [
        'titulo' => 'Error ' . $exception->statusCode,
        'codigo' => $exception->statusCode,
        'mensaje' => $exception->getMessage(),
    ]);
} catch (Throwable $exception) {
    error_log(sprintf('[%s] %s', $exception::class, $exception->getMessage()));
    http_response_code(500);
    View::render('errors/http', [
        'titulo' => 'Error interno',
        'codigo' => 500,
        'mensaje' => 'Ocurrió un error inesperado. Inténtalo nuevamente más tarde.',
    ]);
}
