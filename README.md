# Yaiza Translate — `translate.grupoyaiza.es`

Plataforma de **traducción de audio en tiempo real**: detecta el idioma que se está
hablando (desde cualquier fuente de sonido) y lo reproduce con voz en el idioma que
elija el usuario. Funciona como **web app instalable (PWA)** en PC (Windows/macOS/Linux),
Android e iOS, y puede empaquetarse como **app nativa** para las tiendas (Capacitor).

Stack: **Laravel 13 · PHP 8.3 · MariaDB**. Sin paso de build de Node (los assets son
estáticos en `public/`), pensado para el VPS CyberPanel/OpenLiteSpeed descrito en `DEPLOY.md`.

## Qué incluye

- 🎙️ **Traductor de voz en vivo** (`/translate`): reconocimiento de voz y voz sintética
  en el dispositivo (Web Speech API, gratis) + traducción de texto vía proveedor
  configurable (MyMemory gratis / LibreTranslate / cloud de pago en planes premium).
- 🌍 **+70 idiomas** de origen/destino (tabla `languages`, editable desde el admin).
- 🌐 **Interfaz multi-idioma** (i18n). Español e inglés completos; el resto se
  autocompleta con `php artisan translations:sync`.
- 💳 **Membresías de 3 niveles** (Free / Premium / Pro) con límites de minutos y motor.
- 💸 **Programa de afiliados multinivel** (estilo ganacon): enlace `?ref=`, atribución
  por cookie, comisiones a 3 niveles (30 % / 10 % / 5 %), panel y solicitud de pagos.
- 🛠️ **Panel de administración**: usuarios, planes, idiomas, comisiones/pagos y ajustes SEO.
- 🔎 **SEO premium**: meta + Open Graph + Twitter Cards, `hreflang`, JSON-LD
  (`SoftwareApplication`), `sitemap.xml` y `robots.txt` dinámicos.
- 📲 **PWA**: `manifest.webmanifest`, service worker con shell offline e iconos.

## Puesta en marcha (local)

```bash
composer install
cp .env.example .env
php artisan key:generate
# Para pruebas rápidas con SQLite: DB_CONNECTION=sqlite y `touch database/database.sqlite`
php artisan migrate --seed
php artisan serve
```

Admin inicial (configurable en `.env`): `ADMIN_EMAIL` / `ADMIN_PASSWORD`
(por defecto `gerencianovapasion@gmail.com` / `Cambiar.1234` — **cámbialo**).

## Despliegue en el VPS

Sigue `DEPLOY.md` (sección B para el primer despliegue, C para actualizaciones) o usa
`./deploy.sh` en el servidor. Document root → `public_html/public`. Tras tocar `.env`
ejecuta siempre `php artisan config:cache`.

Configuración clave en `.env`:

| Variable | Para qué |
|---|---|
| `TRANSLATE_PROVIDER` | `mymemory` (gratis) o `libretranslate` |
| `AFFILIATE_L1/L2/L3` | % de comisión por nivel |
| `BILLING_MODE` | `manual` (activa al instante) · `redsys`/`stripe` (ver `docs/BILLING.md`) |

## Idiomas de la interfaz

```bash
php artisan translations:sync          # rellena todos los locales de config('translator.ui_locales')
php artisan translations:sync fr --force
php artisan optimize:clear
```

## Apps nativas (tiendas)

La carpeta `mobile/` contiene la configuración de **Capacitor** que envuelve la PWA.
Ver `mobile/README.md` para generar los binarios de Google Play y App Store.

## Notas técnicas

- El **reconocimiento de voz (STT)** y la **voz sintética (TTS)** usan la Web Speech API
  del navegador: gratis y en el dispositivo. Chrome/Edge/Safari actualizados.
- La captura de **audio del sistema/pestaña** (no solo micrófono) está reservada a planes
  premium y se completa en la app nativa; el navegador limita alimentar audio del sistema
  al reconocedor de voz.
- La traducción de texto se hace en el servidor (`TranslationService`) para no exponer
  claves y poder cambiar de motor por plan.
