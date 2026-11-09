(function () {
    'use strict';

    const form = document.querySelector('[data-validate-form]');

    if (form) {
        const validators = {
            cedula: {
                validate: (value) => /^\d{10}$/.test(value),
                message: 'La cédula debe contener exactamente 10 dígitos.',
            },
            nombres: {
                validate: (value) => value.trim().length >= 2 && value.trim().length <= 80,
                message: 'Los nombres deben tener entre 2 y 80 caracteres.',
            },
            apellidos: {
                validate: (value) => value.trim().length >= 2 && value.trim().length <= 80,
                message: 'Los apellidos deben tener entre 2 y 80 caracteres.',
            },
            correo: {
                validate: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim()),
                message: 'Ingresa un correo electrónico válido.',
            },
            carrera: {
                validate: (value) => value !== '',
                message: 'Selecciona una carrera.',
            },
            semestre: {
                validate: (value) => /^[1-8]$/.test(value),
                message: 'Selecciona un semestre entre 1 y 8.',
            },
            telefono: {
                validate: (value) => /^\+?\d{7,15}$/.test(value.trim()),
                message: 'El teléfono debe tener entre 7 y 15 dígitos; puede iniciar con +.',
            },
        };

        const fieldWrapper = (field) => field.closest('.field');

        const clearClientError = (field) => {
            const wrapper = fieldWrapper(field);

            if (!wrapper) {
                return;
            }

            const previous = wrapper.querySelector('[data-client-error]');

            if (previous) {
                previous.remove();
            }

            field.removeAttribute('aria-invalid');
        };

        const showClientError = (field, message) => {
            const wrapper = fieldWrapper(field);

            if (!wrapper) {
                return;
            }

            clearClientError(field);

            const error = document.createElement('p');
            error.className = 'field-error';
            error.dataset.clientError = 'true';
            error.textContent = message;

            wrapper.appendChild(error);
            field.setAttribute('aria-invalid', 'true');
        };

        const validateField = (field) => {
            const validator = validators[field.name];

            if (!validator) {
                return true;
            }

            const value = field.value;
            const isValid = value.trim() !== '' && validator.validate(value);

            if (!isValid) {
                showClientError(field, validator.message);
            } else {
                clearClientError(field);
            }

            return isValid;
        };

        form.querySelectorAll('input, select').forEach((field) => {
            field.addEventListener('input', () => clearClientError(field));
            field.addEventListener('change', () => clearClientError(field));
            field.addEventListener('blur', () => {
                if (field.value.trim() !== '') {
                    validateField(field);
                }
            });
        });

        form.addEventListener('submit', (event) => {
            let isValid = true;
            let firstInvalid = null;

            form.querySelectorAll('input, select').forEach((field) => {
                if (!validators[field.name]) {
                    return;
                }

                if (!validateField(field)) {
                    isValid = false;

                    if (firstInvalid === null) {
                        firstInvalid = field;
                    }
                }
            });

            if (!isValid) {
                event.preventDefault();

                if (firstInvalid !== null) {
                    firstInvalid.focus();
                }
            }
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((confirmForm) => {
        confirmForm.addEventListener('submit', (event) => {
            if (!window.confirm(confirmForm.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
        button.addEventListener('click', () => {
            const alert = button.closest('[data-alert]');

            if (alert) {
                alert.remove();
            }
        });
    });
})();
