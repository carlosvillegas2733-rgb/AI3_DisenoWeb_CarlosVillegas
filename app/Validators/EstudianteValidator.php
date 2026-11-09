<?php

declare(strict_types=1);

namespace App\Validators;

final class EstudianteValidator
{
    public const CARRERAS = [
        'Ingeniería en Sistemas Inteligentes',
        'Ingeniería en Redes Inteligentes y Ciberseguridad',
        'Ingeniería en Mecatrónica',
        'Ingeniería Industrial',
        'Ingeniería Civil',
        'Arquitectura',
    ];

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    public function normalize(array $data): array
    {
        return [
            'cedula' => preg_replace('/\D/', '', (string) ($data['cedula'] ?? '')) ?? '',
            'nombres' => $this->cleanName((string) ($data['nombres'] ?? '')),
            'apellidos' => $this->cleanName((string) ($data['apellidos'] ?? '')),
            'correo' => mb_strtolower(trim((string) ($data['correo'] ?? ''))),
            'carrera' => trim((string) ($data['carrera'] ?? '')),
            'semestre' => trim((string) ($data['semestre'] ?? '')),
            'telefono' => $this->cleanPhone((string) ($data['telefono'] ?? '')),
        ];
    }

    /**
     * @param array<string, string> $data
     * @return array<string, string>
     */
    public function validate(array $data): array
    {
        $errors = [];

        if ($data['cedula'] === '') {
            $errors['cedula'] = 'La cédula es obligatoria.';
        } elseif (!preg_match('/^\d{10}$/', $data['cedula'])) {
            $errors['cedula'] = 'La cédula debe contener exactamente 10 dígitos.';
        } elseif (!$this->hasValidCheckDigit($data['cedula'])) {
            $errors['cedula'] = 'La cédula ingresada no es válida: el dígito verificador no coincide.';
        }

        if ($data['nombres'] === '') {
            $errors['nombres'] = 'Los nombres son obligatorios.';
        } elseif (mb_strlen($data['nombres']) < 2 || mb_strlen($data['nombres']) > 80) {
            $errors['nombres'] = 'Los nombres deben tener entre 2 y 80 caracteres.';
        } elseif (!preg_match("/^[\p{L}\p{M}\s'.-]+$/u", $data['nombres'])) {
            $errors['nombres'] = 'Los nombres solo pueden incluir letras, espacios, apóstrofes, puntos y guiones.';
        }

        if ($data['apellidos'] === '') {
            $errors['apellidos'] = 'Los apellidos son obligatorios.';
        } elseif (mb_strlen($data['apellidos']) < 2 || mb_strlen($data['apellidos']) > 80) {
            $errors['apellidos'] = 'Los apellidos deben tener entre 2 y 80 caracteres.';
        } elseif (!preg_match("/^[\p{L}\p{M}\s'.-]+$/u", $data['apellidos'])) {
            $errors['apellidos'] = 'Los apellidos solo pueden incluir letras, espacios, apóstrofes, puntos y guiones.';
        }

        if ($data['correo'] === '') {
            $errors['correo'] = 'El correo electrónico es obligatorio.';
        } elseif (mb_strlen($data['correo']) > 120) {
            $errors['correo'] = 'El correo electrónico no puede superar los 120 caracteres.';
        } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'Ingresa un correo electrónico válido.';
        }

        if ($data['carrera'] === '') {
            $errors['carrera'] = 'Selecciona una carrera.';
        } elseif (!in_array($data['carrera'], self::CARRERAS, true)) {
            $errors['carrera'] = 'La carrera seleccionada no es válida.';
        }

        if ($data['semestre'] === '') {
            $errors['semestre'] = 'Selecciona un semestre.';
        } elseif (!preg_match('/^[1-8]$/', $data['semestre'])) {
            $errors['semestre'] = 'El semestre debe estar entre 1 y 8.';
        }

        if ($data['telefono'] === '') {
            $errors['telefono'] = 'El teléfono es obligatorio.';
        } elseif (!preg_match('/^\+?\d{7,15}$/', $data['telefono'])) {
            $errors['telefono'] = 'El teléfono debe tener entre 7 y 15 dígitos (puede iniciar con +).';
        }

        return $errors;
    }

    public function hasValidCheckDigit(string $cedula): bool
    {
        if (!preg_match('/^\d{10}$/', $cedula)) {
            return false;
        }

        $province = (int) substr($cedula, 0, 2);

        if ($province < 1 || ($province > 24 && $province !== 30)) {
            return false;
        }

        if ((int) $cedula[2] > 5) {
            return false;
        }

        $coefficients = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $sum = 0;

        for ($index = 0; $index < 9; $index++) {
            $product = (int) $cedula[$index] * $coefficients[$index];
            $sum += $product > 9 ? $product - 9 : $product;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return $checkDigit === (int) $cedula[9];
    }

    private function cleanName(string $value): string
    {
        $value = trim($value);

        return preg_replace('/\s+/u', ' ', $value) ?? $value;
    }

    private function cleanPhone(string $value): string
    {
        $value = trim($value);
        $hasPlus = str_starts_with($value, '+');
        $digits = preg_replace('/\D/', '', $value) ?? '';

        return ($hasPlus ? '+' : '') . $digits;
    }
}
