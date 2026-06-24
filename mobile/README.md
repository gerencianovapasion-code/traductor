# Apps nativas (Capacitor) — Google Play & App Store

Esta carpeta empaqueta la PWA de Yaiza Translate como app nativa. La estrategia es un
**wrapper** que carga la web en producción (`server.url`), de modo que la app y la web
comparten el mismo código y se actualizan a la vez.

## Requisitos

- Node 18+ (el VPS tiene Node 22) y npm.
- **Android**: Android Studio + JDK 17. Cuenta de Google Play Console (25 $ pago único).
- **iOS**: macOS + Xcode. Cuenta de Apple Developer (99 $/año). *iOS solo se compila en Mac.*

## Pasos

```bash
cd mobile
npm install
mkdir -p www && echo "<!doctype html><meta http-equiv=refresh content=0;url=https://yaizatranslate.grupoyaiza.es/translate>" > www/index.html

npm run add:android      # crea el proyecto Android
npm run add:ios          # crea el proyecto iOS (en Mac)
npm run sync
npm run open:android     # abre Android Studio para generar el AAB firmado
npm run open:ios         # abre Xcode para archivar y subir a App Store Connect
```

## Permisos de micrófono (imprescindible para la traducción de voz)

- **Android** — `android/app/src/main/AndroidManifest.xml`:
  ```xml
  <uses-permission android:name="android.permission.RECORD_AUDIO" />
  <uses-permission android:name="android.permission.INTERNET" />
  ```
- **iOS** — `ios/App/App/Info.plist`:
  ```xml
  <key>NSMicrophoneUsageDescription</key>
  <string>Necesitamos el micrófono para escuchar y traducir el audio en tiempo real.</string>
  <key>NSSpeechRecognitionUsageDescription</key>
  <string>Usamos el reconocimiento de voz para transcribir y traducir lo que escuchas.</string>
  ```

## Iconos y splash

Coloca el icono base en `mobile/resources/icon.png` (1024×1024) y el splash en
`mobile/resources/splash.png` (2732×2732) y genera los tamaños con
`@capacitor/assets`. El icono fuente está en `../public/img/icon-512.png`.

> Nota: el reconocimiento de voz dentro del WebView usa la Web Speech API del sistema.
> En iOS conviene validar el comportamiento en dispositivo real; si se requiere STT nativo,
> añadir un plugin de speech-to-text de Capacitor y exponerlo a la página.
