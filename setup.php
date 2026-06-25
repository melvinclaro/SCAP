<?php
/**
 * SCAP - Script de Instalación
 * ─────────────────────────────
 * Visita: http://localhost/SCAP/setup.php
 * Ejecuta UNA SOLA VEZ para instalar el sistema.
 * ELIMINA O RENOMBRA ESTE ARCHIVO DESPUÉS DE USARLO.
 */

// ── Configuración ─────────────────────────────────────────
$host    = 'localhost';
$user    = 'root';
$pass    = '';          // Contraseña de MySQL (vacía en XAMPP por defecto)
$dbName  = 'scap';
$charset = 'utf8mb4';

$adminUser     = 'admin';
$adminPass     = 'admin123';  // ← Cambia esto antes de ejecutar en producción
$adminNombre   = 'Administrador';
$adminApellido = 'Principal';

// ── Proceso de instalación ────────────────────────────────
$log = [];
$success = true;

try {
    // 1. Conectar sin seleccionar base de datos
    $pdo = new PDO(
        "mysql:host=$host;charset=$charset",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $log[] = ['ok', 'Conexión a MySQL establecida.'];

    // 2. Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    $log[] = ['ok', "Base de datos `$dbName` lista."];

    // 3. Crear tablas
    $pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
        `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        `username`   VARCHAR(50)     NOT NULL,
        `password`   VARCHAR(255)    NOT NULL,
        `nombre`     VARCHAR(100)    NOT NULL,
        `apellido`   VARCHAR(100)    NOT NULL DEFAULT '',
        `activo`     TINYINT(1)      NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = ['ok', 'Tabla `admins` creada.'];

    $pdo->exec("CREATE TABLE IF NOT EXISTS `workers` (
        `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        `cedula`       VARCHAR(15)     NOT NULL,
        `nombre`       VARCHAR(100)    NOT NULL,
        `apellido`     VARCHAR(100)    NOT NULL,
        `cargo`        VARCHAR(100)    NOT NULL DEFAULT 'Obrero',
        `departamento` VARCHAR(100)    NOT NULL DEFAULT 'General',
        `activo`       TINYINT(1)      NOT NULL DEFAULT 1,
        `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_cedula` (`cedula`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = ['ok', 'Tabla `workers` creada.'];

    $pdo->exec("CREATE TABLE IF NOT EXISTS `attendance` (
        `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
        `worker_id`    INT UNSIGNED    NOT NULL,
        `fecha`        DATE            NOT NULL,
        `hora_entrada` TIME            NULL DEFAULT NULL,
        `hora_salida`  TIME            NULL DEFAULT NULL,
        `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uk_worker_fecha` (`worker_id`, `fecha`),
        CONSTRAINT `fk_attendance_worker`
            FOREIGN KEY (`worker_id`) REFERENCES `workers` (`id`)
            ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $log[] = ['ok', 'Tabla `attendance` creada.'];

    // 4. Insertar obreros de prueba (si no existen)
    $count = $pdo->query("SELECT COUNT(*) FROM workers")->fetchColumn();
    if ($count == 0) {
        $workers = [
            ['12345678','Carlos','Rodríguez','Electricista','Mantenimiento'],
            ['23456789','María','González','Pintora','Obras Civiles'],
            ['34567890','José','Martínez','Plomero','Mantenimiento'],
            ['45678901','Ana','López','Albañil','Obras Civiles'],
            ['56789012','Pedro','Ramírez','Carpintero','Taller'],
            ['67890123','Luisa','Torres','Obrera General','General'],
            ['78901234','Miguel','Hernández','Soldador','Taller'],
            ['89012345','Carmen','Flores','Jardinera','Áreas Verdes'],
            ['90123456','Roberto','Díaz','Conductor','Transporte'],
            ['01234567','Yolanda','Pérez','Limpieza','Servicios'],
        ];
        $stmt = $pdo->prepare(
            "INSERT INTO workers (cedula,nombre,apellido,cargo,departamento) VALUES (?,?,?,?,?)"
        );
        foreach ($workers as $w) { $stmt->execute($w); }
        $log[] = ['ok', count($workers) . ' obreros de prueba insertados.'];
    } else {
        $log[] = ['info', 'La tabla `workers` ya tiene datos. Se omite inserción de muestra.'];
    }

    // 5. Crear administrador por defecto
    $exists = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $exists->execute([$adminUser]);
    if (!$exists->fetch()) {
        $hash = password_hash($adminPass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare(
            "INSERT INTO admins (username, password, nombre, apellido) VALUES (?,?,?,?)"
        );
        $stmt->execute([$adminUser, $hash, $adminNombre, $adminApellido]);
        $log[] = ['ok', "Administrador creado → usuario: <strong>$adminUser</strong> | contraseña: <strong>$adminPass</strong>"];
    } else {
        $log[] = ['info', "El usuario `$adminUser` ya existe. No se sobreescribió."];
    }

} catch (PDOException $e) {
    $log[] = ['error', 'Error PDO: ' . $e->getMessage()];
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Instalación · SCAP</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#1e1b4b,#312e81);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:16px;padding:40px;max-width:600px;width:100%;box-shadow:0 25px 50px rgba(0,0,0,.4)}
.logo{text-align:center;margin-bottom:24px}
.logo h1{font-size:28px;font-weight:700;color:#1e1b4b}
.logo p{color:#64748b;font-size:14px;margin-top:4px}
.log-item{display:flex;gap:12px;padding:10px 14px;border-radius:8px;margin-bottom:8px;font-size:14px;align-items:flex-start}
.log-item.ok{background:#f0fdf4;border-left:3px solid #10b981;color:#166534}
.log-item.error{background:#fef2f2;border-left:3px solid #ef4444;color:#991b1b}
.log-item.info{background:#eff6ff;border-left:3px solid #3b82f6;color:#1d4ed8}
.icon{font-size:16px;flex-shrink:0;margin-top:1px}
.result{text-align:center;margin-top:24px;padding:20px;border-radius:12px}
.result.success{background:#f0fdf4;color:#166534}
.result.fail{background:#fef2f2;color:#991b1b}
.result h2{font-size:20px;font-weight:700;margin-bottom:8px}
.result p{font-size:14px;color:inherit;opacity:.8}
.btn{display:inline-block;margin-top:16px;padding:12px 28px;background:linear-gradient(135deg,#6366f1,#818cf8);color:#fff;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;transition:opacity .2s}
.btn:hover{opacity:.9}
.warn{background:#fffbeb;border:1px solid #f59e0b;border-radius:8px;padding:12px 16px;color:#92400e;font-size:13px;margin-top:16px}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <h1>⚙️ SCAP · Instalación</h1>
    <p>Sistema de Control de Asistencia del Personal</p>
  </div>

  <div class="log-list">
    <?php foreach ($log as [$type, $msg]): ?>
      <div class="log-item <?= $type ?>">
        <span class="icon"><?= $type === 'ok' ? '✅' : ($type === 'error' ? '❌' : 'ℹ️') ?></span>
        <span><?= $msg ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="result <?= $success ? 'success' : 'fail' ?>">
    <?php if ($success): ?>
      <h2>✅ Instalación Exitosa</h2>
      <p>El sistema SCAP está listo para usarse.</p>
      <a href="/SCAP/" class="btn">Ir al Sistema →</a>
    <?php else: ?>
      <h2>❌ Error en la Instalación</h2>
      <p>Revisa los mensajes anteriores. Verifica que XAMPP esté corriendo y las credenciales sean correctas en <code>setup.php</code>.</p>
    <?php endif; ?>
  </div>

  <?php if ($success): ?>
  <div class="warn">
    ⚠️ <strong>Importante:</strong> Por seguridad, elimina o renombra <code>setup.php</code> después de la instalación.
    <br>Credenciales de acceso → Usuario: <strong>admin</strong> | Contraseña: <strong>admin123</strong>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
