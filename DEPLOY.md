# Guía de despliegue (Laravel + CyberPanel/OpenLiteSpeed en VPS)

Runbook reutilizable para conectar un repo de GitHub con un VPS y desplegar.
Pensado para el patrón: desarrollo por git (push) → en el VPS se hace `git pull` + comandos.

Sustituye los marcadores: `<dominio>` (ej. `saborlocal.es`), `<usuario_sitio>`
(el usuario Linux del sitio en CyberPanel), `<repo>` (ej. `gerencianovapasion-code/saborlocal`).

---

## A. Conexión GitHub ↔ VPS (una sola vez por VPS)

### 1. Clave SSH en el VPS
Por SSH en el VPS (como root):
```bash
ssh-keygen -t ed25519 -C "vps-deploy" -f ~/.ssh/id_ed25519 -N ""
cat ~/.ssh/id_ed25519.pub
```

### 2. Registrar la clave en GitHub
- **Para 1 solo repo**: repo → Settings → **Deploy keys** → Add deploy key → pega la clave (solo lectura). *Una deploy key sirve para UN repo.*
- **Para varios proyectos en el mismo VPS (recomendado)**: añádela a tu **cuenta**:
  GitHub → Settings (de tu usuario) → **SSH and GPG keys** → New SSH key. Así esa
  clave del VPS puede clonar **todos tus repos** sin crear una por proyecto.

### 3. SSH por el puerto 443 (si el 22 está bloqueado)
Muchos VPS (Hostinger, etc.) bloquean el puerto 22 saliente. Solución una vez:
```bash
mkdir -p ~/.ssh && chmod 700 ~/.ssh
cat >> ~/.ssh/config <<'EOF'
Host github.com
  Hostname ssh.github.com
  Port 443
  User git
EOF
chmod 600 ~/.ssh/config
ssh -T git@github.com   # debe saludar: "Hi <usuario>! You've successfully authenticated"
```

> No usar el **Git Manager** de CyberPanel: suele fallar. Clonar a mano es más fiable.

---

## B. Primer despliegue del proyecto

```bash
cd /home/<dominio>
git config --global --add safe.directory /home/<dominio>/public_html
rm -rf public_html
git clone git@github.com:<repo>.git public_html
cd public_html

PHP=/usr/local/lsws/lsphp83/bin/php      # ajusta a tu versión de PHP

# Dependencias (instala Composer si no está)
which composer || (curl -sS https://getcomposer.org/installer | $PHP -- --install-dir=/usr/local/bin --filename=composer)
$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader

# Entorno
cp .env.example .env
$PHP artisan key:generate
nano .env   # APP_ENV=production, APP_DEBUG=false, APP_URL=https://<dominio>, datos de BD MySQL, MAIL_*

# Base de datos (créala antes en CyberPanel → Databases)
$PHP artisan migrate --force
$PHP artisan storage:link

# Cachés de producción
$PHP artisan config:cache && $PHP artisan route:cache && $PHP artisan view:cache

# Permisos (IMPORTANTE: si ejecutas artisan como root, devuelve el dueño al sitio)
OWNER=$(stat -c '%U' /home/<dominio>)
chown -R "$OWNER:$OWNER" /home/<dominio>/public_html
chmod -R 775 storage bootstrap/cache

# Evita ruido de git por cambios de permisos
git config core.fileMode false
```

### Document root → /public (clave en CyberPanel)
Laravel NO se sirve desde `public_html`, sino desde `public_html/public`:
1. CyberPanel → Websites → Manage (`<dominio>`) → **vHost Conf**.
2. `docRoot                   $VH_ROOT/public_html/public`
3. Guardar → **Restart OpenLiteSpeed**.

### SSL y cron
- SSL: CyberPanel → Manage → **Issue SSL** (Let's Encrypt).
- Tareas programadas (si el proyecto las usa):
  ```cron
  * * * * * cd /home/<dominio>/public_html && /usr/local/lsws/lsphp83/bin/php artisan schedule:run >> /dev/null 2>&1
  ```

---

## C. Despliegues siguientes (cada actualización)

```bash
cd /home/<dominio>/public_html
git pull origin main
PHP=/usr/local/lsws/lsphp83/bin/php

$PHP /usr/local/bin/composer install --no-dev --optimize-autoloader   # si cambiaron dependencias
$PHP artisan migrate --force                                          # si hay migraciones nuevas

$PHP artisan optimize:clear
$PHP artisan config:cache && $PHP artisan route:cache && $PHP artisan view:cache

OWNER=$(stat -c '%U' /home/<dominio>)
chown -R "$OWNER:$OWNER" storage bootstrap/cache
```
Recarga con **Ctrl+F5**. (Hay un `deploy.sh` en la raíz que automatiza parte de esto.)

---

## D. Gotchas aprendidos (evitan horas de líos)

1. **Puerto 22 bloqueado** → SSH por 443 (sección A.3).
2. **Versión de PHP**: el VPS tenía 8.3 y el `composer.lock` se generó con 8.4.
   Se fija en `composer.json`: `"config": { "platform": { "php": "8.3" } }` y se
   regenera el lock (`composer update`). Así no hace falta instalar otro PHP.
3. **Sin Node en el VPS**: se versionan los assets compilados (`public/build` fuera
   del `.gitignore`) para no necesitar `npm` en producción.
4. **500 tras desplegar**: casi siempre permisos. Si corres `artisan` como **root**,
   los archivos de caché quedan de root → `chown` a `<usuario_sitio>` y
   `php artisan optimize:clear`. Para ver el error: `APP_DEBUG=true` temporal o
   `tail -n 40 storage/logs/laravel.log`.
5. **`config:cache`**: tras tocar `.env` SIEMPRE `php artisan config:cache`, o no
   coge los cambios (en producción `env()` devuelve null si la config está cacheada).
6. **Rutas con Closure** y `route:cache`: funcionan porque está
   `laravel/serializable-closure`. Si diera error, mueve esa ruta a un controlador.
7. **Webhooks/pasarelas** (Redsys, etc.): excluir su URL del CSRF en `bootstrap/app.php`.

---

## E. Para replicar en OTRO proyecto / OTRO chat

Dile al nuevo chat algo así:

> "Trabajo por git: tú desarrollas en el repo `<repo>` (rama de trabajo), haces
> push y yo despliego en mi VPS con CyberPanel/OpenLiteSpeed (PHP 8.3, sin Node).
> Sigue el patrón del `DEPLOY.md`: fija la plataforma de Composer a PHP 8.3,
> versiona `public/build`, y dame los comandos de `git pull` para el VPS.
> La conexión SSH del VPS con GitHub ya está hecha (clave en mi cuenta + SSH por 443)."

Si el VPS y la clave de cuenta de GitHub ya están configurados (sección A), para un
proyecto nuevo solo necesitas la **sección B** (clonar + configurar) una vez, y
luego la **sección C** para cada actualización.
