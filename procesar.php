<?php
// ============================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================
// IMPORTANTE: completá el usuario de MySQL.
// En Hostinger suele tener el formato: u331324140_usuario
$host    = "srv847.hstgr.io";
$usuario = "COMPLETAR_USUARIO_MYSQL";   // <-- Poné acá tu usuario de MySQL
$clave   = "Malbor0";
$basedatos = "u331324140_presencias";

// ============================================
// CONEXIÓN
// ============================================
$conexion = new mysqli($host, $usuario, $clave, $basedatos);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

// ============================================
// RECEPCIÓN Y LIMPIEZA DE DATOS DEL FORMULARIO
// ============================================
function valor($campo) {
    // Devuelve el valor recibido o NULL si viene vacío
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
    die("Faltan completar campos obligatorios (nombre, fecha de nacimiento, CUIL, empresa, fecha de alta).");
}

// ============================================
// INSERT CON SENTENCIA PREPARADA (evita SQL injection)
// ============================================
$sql = "INSERT INTO empleados
        (nombre, fecha_nac, est_civil, empresa_id, domicilio, CUIL, telefono,
         nro_legajo, nro_credencial, fecha_venc_cred, hora_entrada, hora_salida,
         pendiente, email, fecha_alta, nacionalidad)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

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

if ($stmt->execute()) {
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Éxito</title>
    <style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f2f5;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
        .caja{background:#fff;padding:40px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.08);text-align:center;max-width:400px;}
        h2{color:#27ae60;}
        a{display:inline-block;margin-top:15px;color:#3498db;text-decoration:none;font-weight:600;}
    </style></head><body>
    <div class='caja'>
        <h2>✅ Empleado guardado correctamente</h2>
        <p>El registro se agregó con el ID " . $conexion->insert_id . ".</p>
        <a href='formulario.html'>Cargar otro empleado</a>
    </div>
    </body></html>";
} else {
    echo "Error al guardar los datos: " . $stmt->error;
}

$stmt->close();
$conexion->close();
?>
