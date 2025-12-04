/* ----------------------------------------------------------------
   GENTLEMEN BARBER SHOP - 2.3 ui.js
   Archivo para efectos visuales adicionales.
---------------------------------------------------------------- */

$(document).ready(function () {

    // 1. Botón "Volver Arriba" (Scroll Top)
    const $scrollTopBtn = $('<button class="btn gentlemen-primary-bg rounded-circle shadow-lg" id="scroll-top-btn" style="display:none; position: fixed; bottom: 20px; right: 20px; z-index: 1050; padding: 0.75rem 1rem;">&#9650;</button>');
    $('body').append($scrollTopBtn);

    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 300) {
            $scrollTopBtn.fadeIn();
        } else {
            $scrollTopBtn.fadeOut();
        }
    });

    $scrollTopBtn.on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        return false;
    });

    // 2. Mostrar/ocultar el menú en móviles (Bootstrap ya lo maneja con su componente)
    // No se necesita JS adicional, solo el markup correcto en el PHP.

    // 3. Mostrar un mensaje emergente (modal) - Para reemplazar alert() y confirm()
    // Usaremos el Modal de Bootstrap para esto, solo necesitamos el HTML en la página principal (index.php)
    // o incluirlo en un componente global (como un header o footer).
});