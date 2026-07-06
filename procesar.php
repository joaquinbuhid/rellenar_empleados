<?php
// Forzamos que cualquier error se muestre, sin depender de la config del servidor
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    // ============================================
    // CONFIGURACIÓN DE LA BASE DE DATOS
    // ============================================
    // IMPORTANTE: completá el usuario de MySQL.
    // En Hostinger suele tener el formato: u331324140_usuario
    $host      = "srv847.hstgr.io";
    $usuario   = "u988721444_rrhh";   // <-- Poné acá tu usuario de MySQL
    $clave     = "#Malbor0";
    $basedatos = "u988721444_personas";

    // ============================================
    // CONEXIÓN
    // ============================================
    $conexion = new mysqli($host, $usuario, $clave, $basedatos);
    $conexion->set_charset("utf8mb4");

    // ============================================
    // RECEPCIÓN Y LIMPIEZA DE DATOS DEL FORMULARIO
    // ============================================
    function valor($campo) {
        if (isset($_POST[$campo]) && trim($_POST[$campo]) !== '') {
            return trim($_POST[$campo]);
        }
        return null;
    }

    $nombre          = valor('nombre');
    $fecha_nac       = valor('fecha_nac');
    $est_civil       = valor('est_civil');
    $empresa_id      = valor('empresa_id');
    $domicilio       = valor('domicilio');
    $cuil            = valor('CUIL');
    $telefono        = valor('telefono');
    $nro_legajo      = valor('nro_legajo');
    $nro_credencial  = valor('nro_credencial');
    $fecha_venc_cred = valor('fecha_venc_cred');
    $hora_entrada    = valor('hora_entrada');
    $hora_salida     = valor('hora_salida');
    $pendiente       = isset($_POST['pendiente']) ? 1 : 0;
    $email           = valor('email');
    $fecha_alta      = valor('fecha_alta');
    $nacionalidad    = valor('nacionalidad');

    // Validación mínima de campos obligatorios
    if (!$nombre || !$fecha_nac || !$cuil || !$empresa_id || !$fecha_alta) {
        throw new Exception("Faltan completar campos obligatorios (nombre, fecha de nacimiento, CUIL, empresa, fecha de alta).");
    }

    // ============================================
    // INSERT CON SENTENCIA PREPARADA
    // ============================================
    $sql = "INSERT INTO empleados
            (nombre, fecha_nac, est_civil, empresa_id, domicilio, CUIL, telefono,
             nro_legajo, nro_credencial, fecha_venc_cred, hora_entrada, hora_salida,
             pendiente, email, fecha_alta, nacionalidad)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "sssisssssssisss",
        $nombre,
        $fecha_nac,
        $est_civil,
        $empresa_id,
        $domicilio,
        $cuil,
        $telefono,
        $nro_legajo,
        $nro_credencial,
        $fecha_venc_cred,
        $hora_entrada,
        $hora_salida,
        $pendiente,
        $email,
        $fecha_alta,
        $nacionalidad
    );

    $stmt->execute();

    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Éxito</title>
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f2f5;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
        .caja{background:#fff;padding:40px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.08);text-align:center;max-width:400px;}
        h2{color:#27ae60;}
        a{display:inline-block;margin-top:15px;color:#3498db;text-decoration:none;font-weight:600;}
    </style></head><body>
    <div class='caja'>
        <h2>Empleado guardado correctamente</h2>
        <p>El registro se agrego con el ID " . $conexion->insert_id . ".</p>
        <a href='formulario.html'>Cargar otro empleado</a>
    </div>
    </body></html>";

    $stmt->close();
    $conexion->close();

} catch (Throwable $e) {
    // Mostramos el error real, cueste lo que cueste
    http_response_code(200); // para que el navegador SI muestre el contenido (evita el 500 en blanco)
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Error</title>
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#fdf0f0;padding:40px;}
        .caja{background:#fff;border-left:5px solid #e74c3c;padding:20px 25px;border-radius:6px;max-width:700px;margin:0 auto;}
        h2{color:#e74c3c;margin-top:0;}
        pre{white-space:pre-wrap;background:#f7f7f7;padding:12px;border-radius:5px;font-size:13px;color:#333;}
        a{color:#3498db;}
    </style></head><body>
    <div class='caja'>
        <h2>Ocurrio un error</h2>
        <pre>" . htmlspecialchars($e->getMessage()) . "</pre>
        <p><a href='formulario.html'>Volver al formulario</a></p>
    </div>
    </body></html>";
}
?>