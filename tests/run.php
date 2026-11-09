<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

use App\Models\Estudiante;
use App\Validators\EstudianteValidator;

final class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;

    /** @var array<int, string> */
    private array $failures = [];

    public function assertTrue(bool $condition, string $description): void
    {
        if ($condition) {
            $this->passed++;
            echo "PASS  {$description}" . PHP_EOL;

            return;
        }

        $this->failed++;
        $this->failures[] = $description;
        echo "FAIL  {$description}" . PHP_EOL;
    }

    public function assertSame(mixed $expected, mixed $actual, string $description): void
    {
        $this->assertTrue(
            $expected === $actual,
            $description . ($expected === $actual ? '' : sprintf(
                ' (expected: %s, actual: %s)',
                var_export($expected, true),
                var_export($actual, true)
            ))
        );
    }

    public function assertContains(string $needle, string $haystack, string $description): void
    {
        $this->assertTrue(str_contains($haystack, $needle), $description);
    }

    public function summary(): int
    {
        echo PHP_EOL . str_repeat('-', 72) . PHP_EOL;
        echo sprintf('Results: %d passed, %d failed, %d total', $this->passed, $this->failed, $this->passed + $this->failed) . PHP_EOL;

        if ($this->failed > 0) {
            echo 'Failed tests:' . PHP_EOL;

            foreach ($this->failures as $failure) {
                echo "  - {$failure}" . PHP_EOL;
            }

            return 1;
        }

        echo 'All tests passed.' . PHP_EOL;

        return 0;
    }
}

$runner = new TestRunner();
$validator = new EstudianteValidator();

$validData = [
    'cedula' => '0143833754',
    'nombres' => 'Camila Fernanda',
    'apellidos' => 'Guerrero Alvarado',
    'correo' => 'Camila.Guerrero@ECOTEC.edu.ec',
    'carrera' => 'Ingeniería Industrial',
    'semestre' => '7',
    'telefono' => '(090) 987-6543',
];

$normalized = $validator->normalize($validData);

$runner->assertSame('0143833754', $normalized['cedula'], 'Normalizes the cédula to ten digits');
$runner->assertSame('camila.guerrero@ecotec.edu.ec', $normalized['correo'], 'Normalizes the email to lowercase');
$runner->assertSame('0909876543', $normalized['telefono'], 'Normalizes the phone number to digits');
$runner->assertSame([], $validator->validate($normalized), 'Accepts a complete valid student record');
$runner->assertTrue($validator->hasValidCheckDigit('1133468080'), 'Accepts a valid Ecuadorian cédula check digit');
$runner->assertTrue(!$validator->hasValidCheckDigit('1234567890'), 'Rejects an invalid Ecuadorian cédula check digit');

$emptyErrors = $validator->validate($validator->normalize([]));
$runner->assertTrue(isset($emptyErrors['cedula']), 'Rejects an empty cédula');
$runner->assertTrue(isset($emptyErrors['nombres']), 'Rejects empty names');
$runner->assertTrue(isset($emptyErrors['apellidos']), 'Rejects empty surnames');
$runner->assertTrue(isset($emptyErrors['correo']), 'Rejects an empty email');
$runner->assertTrue(isset($emptyErrors['carrera']), 'Rejects an empty career');
$runner->assertTrue(isset($emptyErrors['semestre']), 'Rejects an empty semester');
$runner->assertTrue(isset($emptyErrors['telefono']), 'Rejects an empty phone');

$invalidErrors = $validator->validate($validator->normalize([
    'cedula' => '1234567890',
    'nombres' => '<script>alert(1)</script>',
    'apellidos' => 'Prueba',
    'correo' => 'correo-invalido',
    'carrera' => 'Carrera inexistente',
    'semestre' => '12',
    'telefono' => 'abc',
]));

$runner->assertTrue(isset($invalidErrors['cedula']), 'Rejects an invalid cédula');
$runner->assertTrue(isset($invalidErrors['nombres']), 'Rejects names with HTML characters');
$runner->assertTrue(isset($invalidErrors['correo']), 'Rejects an invalid email');
$runner->assertTrue(isset($invalidErrors['carrera']), 'Rejects an unknown career');
$runner->assertTrue(isset($invalidErrors['semestre']), 'Rejects a semester outside 1-8');
$runner->assertTrue(isset($invalidErrors['telefono']), 'Rejects an invalid phone');

$runner->assertSame(
    '&lt;script&gt;alert(1)&lt;/script&gt;',
    e('<script>alert(1)</script>'),
    'Escapes HTML output to prevent XSS'
);

$pdo = conexion();
$repository = new Estudiante($pdo);

$runner->assertTrue($repository->count() >= 10, 'Reads the seeded student count');
$runner->assertTrue($repository->cedulaExists('1133468080'), 'Detects an existing cédula');
$runner->assertTrue($repository->emailExists('andrea.mendoza@ecotec.edu.ec'), 'Detects an existing email');
$runner->assertTrue(count($repository->all('Mendoza')) >= 1, 'Searches students by surname');
$runner->assertTrue($repository->find(999999) === null, 'Returns null for a missing student');

$pdo->beginTransaction();

try {
    $testData = $validator->normalize($validData);
    $newId = $repository->create($testData);

    $runner->assertTrue($newId > 0, 'Creates a student with prepared statements');

    $created = $repository->find($newId);
    $runner->assertTrue($created !== null, 'Finds the newly created student');
    $runner->assertSame('Camila Fernanda', $created['nombres'] ?? null, 'Persists the student names');
    $runner->assertSame('0909876543', $created['telefono'] ?? null, 'Persists the normalized phone');

    $updatedData = $testData;
    $updatedData['nombres'] = 'Camila Fernanda Actualizada';
    $updatedData['semestre'] = '8';

    $runner->assertTrue($repository->update($newId, $updatedData), 'Updates the student with prepared statements');

    $updated = $repository->find($newId);
    $runner->assertSame('Camila Fernanda Actualizada', $updated['nombres'] ?? null, 'Persists the updated names');
    $runner->assertSame('8', (string) ($updated['semestre'] ?? ''), 'Persists the updated semester');

    $runner->assertTrue($repository->delete($newId), 'Deletes the student with prepared statements');
    $runner->assertTrue($repository->find($newId) === null, 'Confirms the deleted student no longer exists');
} finally {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}

exit($runner->summary());
