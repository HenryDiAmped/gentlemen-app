/* ----------------------------------------------------------------
   GENTLEMEN BARBER SHOP - 2.2 forms.js
   Lógica específica para validación de formularios.
---------------------------------------------------------------- */

$(document).ready(function () {

    // Función para mostrar mensaje de error
    function mostrarError(element, message) {
        // Remover errores previos
        element.parent().find('.mensaje-error').remove();
        // Mostrar nuevo error
        element.after(`<span class="mensaje-error">${message}</span>`);
        element.addClass('is-invalid');
    }

    // Función para limpiar errores
    function limpiarError(element) {
        element.parent().find('.mensaje-error').remove();
        element.removeClass('is-invalid').addClass('is-valid');
    }

    // Validación genérica del formulario de Login (ejemplo)
    $('#login-form').on('submit', function (e) {
        let valid = true;

        const $email = $('#email');
        const $password = $('#password');

        // 1. Verificación de campos obligatorios
        if ($email.val().trim() === '') {
            mostrarError($email, 'El email es obligatorio.');
            valid = false;
        } else {
            limpiarError($email);
        }

        if ($password.val() === '') {
            mostrarError($password, 'La contraseña es obligatoria.');
            valid = false;
        } else if ($password.val().length < 8) {
            // RN-001: Contraseña de mínimo 8 caracteres
            mostrarError($password, 'La contraseña debe tener al menos 8 caracteres.');
            valid = false;
        } else {
            limpiarError($password);
        }

        // 2. Validación básica de formato de correo electrónico
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test($email.val()) && valid) {
            mostrarError($email, 'Formato de correo inválido.');
            valid = false;
        } else if (valid) {
            limpiarError($email);
        }

        // Impedir el envío del formulario si hay errores
        if (!valid) {
            e.preventDefault();
        }
    });

    // Limpiar errores mientras el usuario teclea
    $('input, textarea').on('input', function () {
        if ($(this).hasClass('is-invalid')) {
            limpiarError($(this));
        }
    });
});