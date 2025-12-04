/* ----------------------------------------------------------------
   GENTLEMEN BARBER SHOP - 2.1 main.js
   Archivo principal que inicializa comportamientos generales usando jQuery.
---------------------------------------------------------------- */

$(document).ready(function () {
    // 1. Aquí va todo el código jQuery del sitio

    // 2. Mostrar el año actual en el pie de página automáticamente (RN-020)
    const currentYear = new Date().getFullYear();
    $('#anio-actual').text(currentYear);

    // 3. Efecto scroll suave al hacer click en enlaces internos (Si existieran en la versión final)
    $('a[href^="#"]').on('click', function (event) {
        // Verificar que sea un enlace interno y no solo '#'
        if (this.hash !== "") {
            event.preventDefault();
            const hash = this.hash;
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 800, function () {
                // Opcional: Agregar el hash al URL después del scroll
                // window.location.hash = hash;
            });
        }
    });

    // 4. Resaltar en el menú la página actual
    const path = window.location.search;
    const navLinks = $('.navbar-nav .nav-link');

    navLinks.each(function () {
        const linkHref = $(this).attr('href');
        if (linkHref === `index.php${path}`) {
            $(this).addClass('active');
        } else if (path === '' && linkHref === 'index.php?page=login') {
            // Caso especial: Si no hay query string, asumimos que va a login
            $(this).addClass('active');
        }
    });
    // Nota: El resaltado de la página actual en Bootstrap (en el código PHP) ya lo manejará
    // la lógica de Bootstrap que usaremos. Este código JS es un respaldo.
});