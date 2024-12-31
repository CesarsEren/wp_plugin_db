<?php
/**
 * Plugin Name: Mi Plugin ErenDB Plugin Database
 * Description: Un plugin con interfaz de configuración para conectarse a una base de datos externa.
 * Version: 1.0.1
 * Author: Cesar's Pinedo
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Crear un menú en el panel de administración
function mi_plugin_crear_menu() {
    add_menu_page(
        'Configuración de Base de Datos',
        'ErenDB Plugin Database',
        'manage_options',
        'mi-plugin-database',
        'mi_plugin_pagina_configuracion',
        'dashicons-database',
        90
    );
}
add_action('admin_menu', 'mi_plugin_crear_menu');

// Mostrar la página de configuración
function mi_plugin_pagina_configuracion() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        return;
    }

    // Guardar configuraciones si el formulario es enviado
    if (isset($_POST['mi_plugin_guardar'])) {
        update_option('mi_plugin_db_host', sanitize_text_field($_POST['db_host']));
        update_option('mi_plugin_db_user', sanitize_text_field($_POST['db_user']));
        update_option('mi_plugin_db_password', sanitize_text_field($_POST['db_password']));
        update_option('mi_plugin_db_name', sanitize_text_field($_POST['db_name']));
        echo '<div class="updated"><p>Configuración guardada.</p></div>';
    }

    // Obtener valores actuales
    $db_host = get_option('mi_plugin_db_host', 'localhost');
    $db_user = get_option('mi_plugin_db_user', '');
    $db_password = get_option('mi_plugin_db_password', '');
    $db_name = get_option('mi_plugin_db_name', '');

    // Formulario de configuración
    ?>
    <div class="wrap">
        <h1>Configuración de Base de Datos</h1>
        <form method="POST" action="">
            <table class="form-table">
                <tr>
                    <th><label for="db_host">Host de la Base de Datos</label></th>
                    <td><input type="text" id="db_host" name="db_host" value="<?php echo esc_attr($db_host); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="db_user">Usuario</label></th>
                    <td><input type="text" id="db_user" name="db_user" value="<?php echo esc_attr($db_user); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="db_password">Contraseña</label></th>
                    <td><input type="password" id="db_password" name="db_password" value="<?php echo esc_attr($db_password); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="db_name">Nombre de la Base de Datos</label></th>
                    <td><input type="text" id="db_name" name="db_name" value="<?php echo esc_attr($db_name); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" name="mi_plugin_guardar" class="button button-primary">Guardar Configuración</button>
            </p>
        </form>
    </div>
    <?php
}

// Ejemplo: Usar los valores configurados para conectarse a la base de datos externa
function mi_plugin_ejecutar_consulta_personalizada() {
    global $wpdb;

    // Obtener valores de configuración
    $db_host = get_option('mi_plugin_db_host', 'localhost');
    $db_user = get_option('mi_plugin_db_user', '');
    $db_password = get_option('mi_plugin_db_password', '');
    $db_name = get_option('mi_plugin_db_name', '');

    // Crear una conexión manual a la base de datos externa
    $conexion = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conexion->connect_error) {
        return 'Error de conexión: ' . $conexion->connect_error;
    }

    // Consulta de ejemplo
    $resultado = $conexion->query('SELECT NOW() AS fecha_actual');
    if ($resultado && $fila = $resultado->fetch_assoc()) {
        return 'La fecha actual en la base de datos es: ' . esc_html($fila['fecha_actual']);
    } else {
        return 'Error al realizar la consulta.';
    }
}

// Crear un shortcode para probar la conexión
add_shortcode('mi_plugin_prueba_conexion', 'mi_plugin_ejecutar_consulta_personalizada');

function mi_plugin_mostrar_categorias() {
    global $wpdb;

    // Obtener configuraciones de la base de datos
    $db_host = get_option('mi_plugin_db_host', 'localhost');
    $db_user = get_option('mi_plugin_db_user', '');
    $db_password = get_option('mi_plugin_db_password', '');
    $db_name = get_option('mi_plugin_db_name', '');

    // Conectar a la base de datos externa
    $conexion = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conexion->connect_error) {
        return 'Error de conexión: ' . esc_html($conexion->connect_error);
    }

    // Consulta para obtener las categorías
    $query = 'SELECT id, descripcion, activo FROM categoria';
    $resultado = $conexion->query($query);

    if ($resultado) {
        // Construir la tabla HTML
        $tabla = '<table style="border-collapse: collapse; width: 100%;">';
        $tabla .= '<thead>';
        $tabla .= '<tr>';
        $tabla .= '<th style="border: 1px solid #ddd; padding: 8px;">ID</th>';
        $tabla .= '<th style="border: 1px solid #ddd; padding: 8px;">Descripción</th>';
        $tabla .= '<th style="border: 1px solid #ddd; padding: 8px;">Activo</th>';
        $tabla .= '</tr>';
        $tabla .= '</thead>';
        $tabla .= '<tbody>';

        while ($fila = $resultado->fetch_assoc()) {
            $tabla .= '<tr>';
            $tabla .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($fila['id']) . '</td>';
            $tabla .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html($fila['descripcion']) . '</td>';
            $tabla .= '<td style="border: 1px solid #ddd; padding: 8px;">' . ($fila['activo'] ? 'Sí' : 'No') . '</td>';
            $tabla .= '</tr>';
        }

        $tabla .= '</tbody>';
        $tabla .= '</table>';

        return $tabla;
    } else {
        return 'Error al realizar la consulta: ' . esc_html($conexion->error);
    }
}
add_shortcode('mi_plugin_mostrar_categorias', 'mi_plugin_mostrar_categorias');

// Registramos el shortcode para mostrar el contenido de un post por su slug
function mostrar_post_por_slug($atts) {
    global $wpdb;

    // Extraemos los atributos del shortcode
    $atts = shortcode_atts(
        array(
            'slug' => '', // Slug del post (por defecto está vacío)
        ),
        $atts,
        'mostrar_post' // Nombre del shortcode
    );

    // Comprobamos si se ha proporcionado un slug
    if (empty($atts['slug'])) {
        return 'Por favor, proporciona un slug de post válido.';
    }

    // Consultamos la base de datos por el slug del post
    $post = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM wp_blog_posts WHERE slug = %s AND status = 'published'",
            $atts['slug']
        )
    );

    // Verificamos si se ha encontrado el post
    if ($post) {
        // Retornamos el contenido del post
        return '<h2>' . esc_html($post->title) . '</h2>' . '<p>' . nl2br(esc_html($post->content)) . '</p>';
    } else {
        return 'No se encontró el post con el slug especificado o el post no está publicado.';
    }
}

// Registramos el shortcode
add_shortcode('mostrar_post', 'mostrar_post_por_slug');
