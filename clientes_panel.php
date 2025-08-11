<?php
// clientes_panel.php
session_start();
if (!isset($_SESSION['uid'])) {
  header('Location: /clientes.html'); exit;
}

$rol = $_SESSION['rol'] ?? 'visor';
$uid = (int)($_SESSION['uid'] ?? 0);
$oid = (int)($_SESSION['oid'] ?? 0);

// Carga datos básicos del usuario/organización (SQLite por ahora)
$user = ['usuario' => ''];
$org  = ['nombre' => '', 'tipo' => ''];
try {
  // AJUSTA la ruta a tu .db si está en otra carpeta:
  $pdo = new PDO('sqlite:' . __DIR__ . '/reloj_control.db');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $st = $pdo->prepare("SELECT usuario, rol FROM portal_usuarios WHERE id=? LIMIT 1");
  $st->execute([$uid]);
  $user = $st->fetch(PDO::FETCH_ASSOC) ?: $user;

  $st = $pdo->prepare("SELECT nombre, tipo FROM organizaciones WHERE id=? LIMIT 1");
  $st->execute([$oid]);
  $org = $st->fetch(PDO::FETCH_ASSOC) ?: $org;
} catch (Throwable $e) {
  // Opcional: log de error
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Cliente - BioAccess</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;500;700&display=swap" rel="stylesheet" />
  <style>
    .panel-wrap{ max-width:1100px; margin:120px auto 40px; padding:0 20px; }
    .panel-header{ display:flex; flex-wrap:wrap; gap:16px; align-items:center; justify-content:space-between; margin-bottom:20px; }
    .chip{ background:#12181f; border:1px solid rgba(52,209,182,.3); color:#34d1b6; padding:8px 12px; border-radius:999px; font-size:14px; }
    .grid{ display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:20px; }
    .card{ background:#0e1217; border-radius:12px; padding:20px; box-shadow:0 8px 20px rgba(52,209,182,.08); border:1px solid rgba(255,255,255,.06); }
    .card h3{ color:#34d1b6; margin:0 0 8px; }
    .card p{ color:#ccc; margin:0 0 14px; font-size:15px; }
    .btn{ display:inline-block; padding:10px 14px; border-radius:10px; background:#34d1b6; color:#000; text-decoration:none; font-weight:600; }
    .btn-outline{ background:transparent; color:#34d1b6; border:1px solid rgba(52,209,182,.5); }
    .toolbar{ display:flex; gap:10px; flex-wrap:wrap; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="nav-content">
      <div class="logo-nav"><img src="/solologo.png" alt="Logo BioAccess" /></div>
      <ul>
        <li><a href="/index.html">Inicio</a></li>
        <li><a href="/api/logout.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </nav>

  <main class="panel-wrap">
    <div class="panel-header">
      <div>
        <h1 style="color:#34d1b6; margin:0;">Panel de Cliente</h1>
        <p style="color:#ccc; margin:6px 0 0;">
          <strong>Organización:</strong> <?= htmlspecialchars($org['nombre'] ?: '—') ?>
          · <strong>Tipo:</strong> <?= htmlspecialchars($org['tipo'] ?: '—') ?>
        </p>
      </div>
      <div class="toolbar">
        <span class="chip">Usuario: <?= htmlspecialchars($user['usuario'] ?: '—') ?></span>
        <span class="chip">Rol: <?= htmlspecialchars($rol) ?></span>
      </div>
    </div>

    <!-- Tarjetas iniciales: listas para conectar más adelante -->
    <div class="grid">
      <section class="card">
        <h3>Reportes de Asistencia</h3>
        <p>Consulta asistencia por rango de fechas, exporta a PDF/Excel y filtra por funcionario.</p>
        <a class="btn" href="#">Ver reportes</a>
      </section>

      <section class="card">
        <h3>Descargas</h3>
        <p>Accede a documentos generados (PDF/ZIP) y últimos respaldos disponibles.</p>
        <a class="btn" href="#">Ir a descargas</a>
      </section>

      <section class="card">
        <h3>Respaldo</h3>
        <p>Sube un respaldo manual desde la app de escritorio o descarga copias automáticas.</p>
        <a class="btn btn-outline" href="#">Subir respaldo</a>
      </section>
    </div>
  </main>
</body>
</html>
