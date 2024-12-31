
<?php
/**
 * Plugin Name: My Dynamic Shortcodes
 * Description: Un plugin para crear shortcodes dinámicamente.
 * Version: 1.0.2
 * Author: Cesar's Pinedo
 */

// Evitar el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Registramos el shortcode al activar el plugin
function mds_register_shortcodes() {
    // Registramos un shortcode básico
    add_shortcode('mi_shortcode', 'mds_shortcode_handler');
}
add_action('init', 'mds_register_shortcodes');

// Función del shortcode
function mds_shortcode_handler($atts) {
    // Aquí definimos el comportamiento del shortcode
    return '<p>¡Este es un shortcode dinámico!</p>';
}

// Agregar una página de configuración para agregar nuevos shortcodes
function mds_add_admin_page() {
    add_menu_page(
        'Dynamic Shortcodes',    // Título de la página
        'Shortcodes Dinámicos',   // Nombre del menú
        'manage_options',         // Capacidad necesaria
        'mds_dynamic_shortcodes', // Slug del menú
        'mds_shortcode_page_html', // Función que mostrará el contenido
        'dashicons-shortcode',    // Icono
        20                        // Posición en el menú
    );
}
add_action('admin_menu', 'mds_add_admin_page');

// Función para mostrar el formulario de configuración
function mds_shortcode_page_html() {
    ?>
    <div class="wrap">
        <h1>Crear Nuevo Shortcode</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th><label for="shortcode_name">Nombre del Shortcode</label></th>
                    <td><input type="text" name="shortcode_name" id="shortcode_name" value=""></td>
                </tr>
                <tr>
                    <th><label for="shortcode_content">Contenido del Shortcode</label></th>
                    <td><textarea name="shortcode_content" id="shortcode_content"></textarea></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="save_shortcode" class="button-primary" value="Crear Shortcode">
            </p>
        </form>
    </div>
    <?php

    // Guardar el nuevo shortcode
    if (isset($_POST['save_shortcode'])) {
        $shortcode_name = sanitize_text_field($_POST['shortcode_name']);
        $shortcode_content = sanitize_textarea_field($_POST['shortcode_content']);
        
        if (!empty($shortcode_name) && !empty($shortcode_content)) {
            // Registrar el nuevo shortcode
            mds_register_new_shortcode($shortcode_name, $shortcode_content);
        }
    }
}

// Función para registrar un nuevo shortcode
function mds_register_new_shortcode($shortcode_name, $shortcode_content) {
    // Registramos el shortcode dinámicamente
    add_shortcode($shortcode_name, function() use ($shortcode_content) {
        return '<div class="mds-shortcode-content">' . esc_html($shortcode_content) . '</div>';
    });

    // Mensaje de éxito
    echo '<div class="updated"><p>Nuevo shortcode registrado: [' . esc_html($shortcode_name) . ']</p></div>';
}
?>