<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
    /**
     * Renders a view inside the shared layout.
     *
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = []): void
    {
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException(sprintf('View not found: %s', $view));
        }

        extract($data, EXTR_SKIP);

        require BASE_PATH . '/app/Views/layout/header.php';
        require $viewFile;
        require BASE_PATH . '/app/Views/layout/footer.php';
    }
}
