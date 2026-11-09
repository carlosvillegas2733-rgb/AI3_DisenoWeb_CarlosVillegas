<?php

declare(strict_types=1);

/**
 * Escapes a value for safe HTML output.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Builds an application URL for the given action.
 *
 * @param array<string, scalar> $params
 */
function url(string $action = 'listar', array $params = []): string
{
    $query = array_merge(['accion' => $action], $params);

    return 'index.php?' . http_build_query($query);
}

/**
 * Redirects to an application action and stops the current request.
 *
 * @param array<string, scalar> $params
 */
function redirect(string $action = 'listar', array $params = []): never
{
    header('Location: ' . url($action, $params));
    exit;
}
