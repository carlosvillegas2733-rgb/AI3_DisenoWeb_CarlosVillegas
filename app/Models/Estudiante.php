<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

final class Estudiante
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(string $search = ''): array
    {
        $sql = 'SELECT id, cedula, nombres, apellidos, correo, carrera, semestre, telefono, fecha_creacion
                FROM estudiantes';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE LOCATE(?, cedula) > 0
                        OR LOCATE(?, nombres) > 0
                        OR LOCATE(?, apellidos) > 0
                        OR LOCATE(?, correo) > 0
                        OR LOCATE(?, carrera) > 0';
            $params = array_fill(0, 5, $search);
        }

        $sql .= ' ORDER BY apellidos ASC, nombres ASC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, cedula, nombres, apellidos, correo, carrera, semestre, telefono,
                    fecha_creacion, fecha_actualizacion
             FROM estudiantes
             WHERE id = :id
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $student = $statement->fetch();

        return $student === false ? null : $student;
    }

    /**
     * @param array<string, string> $data
     */
    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO estudiantes (cedula, nombres, apellidos, correo, carrera, semestre, telefono)
             VALUES (:cedula, :nombres, :apellidos, :correo, :carrera, :semestre, :telefono)'
        );
        $statement->execute($this->bindings($data));

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @param array<string, string> $data
     */
    public function update(int $id, array $data): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE estudiantes
             SET cedula = :cedula,
                 nombres = :nombres,
                 apellidos = :apellidos,
                 correo = :correo,
                 carrera = :carrera,
                 semestre = :semestre,
                 telefono = :telefono
             WHERE id = :id'
        );

        return $statement->execute($this->bindings($data) + ['id' => $id]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM estudiantes WHERE id = :id');

        return $statement->execute(['id' => $id]);
    }

    public function cedulaExists(string $cedula, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM estudiantes WHERE cedula = :cedula';
        $params = ['cedula' => $cedula];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM estudiantes WHERE correo = :correo';
        $params = ['correo' => $email];

        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM estudiantes')->fetchColumn();
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    private function bindings(array $data): array
    {
        return [
            'cedula' => $data['cedula'],
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'correo' => $data['correo'],
            'carrera' => $data['carrera'],
            'semestre' => $data['semestre'],
            'telefono' => $data['telefono'],
        ];
    }
}
