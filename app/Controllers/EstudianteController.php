<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\HttpException;
use App\Core\View;
use App\Models\Estudiante;
use App\Validators\EstudianteValidator;

final class EstudianteController
{
    private readonly Estudiante $repository;
    private readonly EstudianteValidator $validator;

    public function __construct()
    {
        $this->repository = new Estudiante(conexion());
        $this->validator = new EstudianteValidator();
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['buscar'] ?? ''));

        View::render('estudiantes/index', [
            'titulo' => 'Estudiantes registrados',
            'estudiantes' => $this->repository->all($search),
            'busqueda' => $search,
            'total' => $this->repository->count(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm(
            'Registrar estudiante',
            'guardar',
            $this->emptyStudent(),
            [],
            false
        );
    }

    public function store(): void
    {
        $this->requireMethod('POST');
        Csrf::validateOrFail($_POST['_csrf_token'] ?? null);

        $data = $this->validator->normalize($_POST);
        $errors = $this->validator->validate($data);
        $errors = $this->uniquenessErrors($data, $errors);

        if ($errors !== []) {
            $this->renderForm('Registrar estudiante', 'guardar', $data, $errors, false);

            return;
        }

        $this->repository->create($data);
        Flash::success('Estudiante registrado correctamente.');
        redirect('listar');
    }

    public function edit(): void
    {
        $id = $this->idFromQuery();
        $student = $this->repository->find($id);

        if ($student === null) {
            throw new HttpException(404, 'El estudiante solicitado no existe.');
        }

        $this->renderForm('Editar estudiante', 'actualizar', $student, [], true);
    }

    public function update(): void
    {
        $this->requireMethod('POST');
        Csrf::validateOrFail($_POST['_csrf_token'] ?? null);

        $id = $this->idFromPost();

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'El estudiante solicitado no existe.');
        }

        $data = $this->validator->normalize($_POST);
        $errors = $this->validator->validate($data);
        $errors = $this->uniquenessErrors($data, $errors, $id);

        if ($errors !== []) {
            $this->renderForm(
                'Editar estudiante',
                'actualizar',
                ['id' => (string) $id] + $data,
                $errors,
                true
            );

            return;
        }

        $this->repository->update($id, $data);
        Flash::success('Estudiante actualizado correctamente.');
        redirect('listar');
    }

    public function destroy(): void
    {
        $this->requireMethod('POST');
        Csrf::validateOrFail($_POST['_csrf_token'] ?? null);

        $id = $this->idFromPost();

        if ($this->repository->find($id) === null) {
            throw new HttpException(404, 'El estudiante solicitado no existe.');
        }

        $this->repository->delete($id);
        Flash::success('Estudiante eliminado correctamente.');
        redirect('listar');
    }

    /**
     * @param array<string, string> $student
     * @param array<string, string> $errors
     */
    private function renderForm(
        string $title,
        string $action,
        array $student,
        array $errors,
        bool $isEdit
    ): void {
        View::render('estudiantes/form', [
            'titulo' => $title,
            'accion' => $action,
            'estudiante' => $student,
            'errores' => $errors,
            'esEdicion' => $isEdit,
            'carreras' => EstudianteValidator::CARRERAS,
        ]);
    }

    /**
     * @param array<string, string> $data
     * @param array<string, string> $errors
     * @return array<string, string>
     */
    private function uniquenessErrors(array $data, array $errors, ?int $excludeId = null): array
    {
        if (!isset($errors['cedula']) && $this->repository->cedulaExists($data['cedula'], $excludeId)) {
            $errors['cedula'] = 'Ya existe un estudiante registrado con esta cédula.';
        }

        if (!isset($errors['correo']) && $this->repository->emailExists($data['correo'], $excludeId)) {
            $errors['correo'] = 'Ya existe un estudiante registrado con este correo.';
        }

        return $errors;
    }

    private function requireMethod(string $method): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            throw new HttpException(405, 'El método HTTP no está permitido para esta acción.');
        }
    }

    private function idFromQuery(): int
    {
        return $this->validateId($_GET['id'] ?? null);
    }

    private function idFromPost(): int
    {
        return $this->validateId($_POST['id'] ?? null);
    }

    private function validateId(mixed $value): int
    {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($id === false) {
            throw new HttpException(400, 'El identificador del estudiante no es válido.');
        }

        return (int) $id;
    }

    /**
     * @return array<string, string>
     */
    private function emptyStudent(): array
    {
        return [
            'id' => '',
            'cedula' => '',
            'nombres' => '',
            'apellidos' => '',
            'correo' => '',
            'carrera' => '',
            'semestre' => '',
            'telefono' => '',
        ];
    }
}
