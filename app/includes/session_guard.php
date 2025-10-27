<?php
// MANEJO DE SESIONES
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if (!isset($_SESSION['usuario'])) {
   // Si hay front controller, redirige a la ruta /login; de lo contrario, login.php
   $target = '/login';
   if (php_sapi_name() === 'cli-server') { $target = '/login'; }
   header("Location: {$target}");
   exit;
}
// FIN MANEJO DE SESIONES
