# CitasYa PHP

Migración de las interfaces de CitasYa a PHP 7 + MySQL.

## Requisitos

- PHP 7.4 o superior con extensiones `pdo_mysql`
- MySQL 8.x
- Composer opcional (no se utiliza en este proyecto)

## Instalación

1. Clona el repositorio y copia `.env.example` a `.env`, ajustando las variables de entorno de la base de datos y los enlaces de Mercado Pago.
2. Crea la base de datos en MySQL:

   ```sql
   CREATE DATABASE citasya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Ejecuta el script de migración:

   ```bash
   mysql -u root -p citasya < scripts/migrate.sql
   ```

4. Carga los catálogos y usuarios de prueba:

   ```bash
   php scripts/seed.php
   ```

5. (Opcional) Genera un snapshot de estadísticas manual:

   ```bash
   php scripts/cron_build_stats.php
   ```

6. Inicia el servidor embebido de PHP:

   ```bash
   php -S localhost:8000 -t public
   ```

7. Abre `http://localhost:8000` en tu navegador.

## Estructura

- `public/`: Front controller y assets.
- `app/`: Configuración, núcleo, modelos, controladores y vistas.
- `scripts/`: utilidades para migrar, sembrar datos y generar estadísticas.
- `docs/db/`: definición DBML de la base de datos.
- `storage/`: carpetas para logs y cache.

## Usuarios de prueba

- Cliente: `usuario / usuario`
- Barbero: `barbero / barbero`
- Admin: `admin / admin`

## Notas

- Todas las solicitudes POST incluyen protección CSRF.
- Las contraseñas se almacenan con `password_hash` y existe un flujo de recuperación en `/password/forgot` con tokens de 30 minutos.
- Configura `CRYPTO_KEY` en `.env` (formato `base64:...`) si necesitas cifrar otros secretos con `App\\Core\\Crypto`.
- Las vistas conservan el estilo original, reutilizando includes comunes para banner, footer y avisos legales.
