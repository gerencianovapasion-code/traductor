# Facturación / pasarela de pago

Por defecto el proyecto arranca en `BILLING_MODE=manual`: al elegir un plan de pago la
suscripción se **activa al instante** (útil para lanzar con pago por transferencia o para
pruebas) y se generan las comisiones de afiliado. Para cobrar de verdad antes de activar,
conecta una pasarela.

## Punto de integración

Todo el flujo pasa por `App\Http\Controllers\SubscriptionController@subscribe`. Hoy crea la
`Subscription` y, si `BILLING_MODE=manual`, la marca `active` y llama a
`AffiliateService::commissionForSubscription()`. Para una pasarela real:

1. Crear la suscripción en estado `pending`.
2. Redirigir a la pasarela (Redsys/Stripe) con el importe del plan.
3. En el **webhook** de confirmación (`POST /webhooks/...`, ya excluido de CSRF en
   `bootstrap/app.php`): marcar la suscripción `active`, fijar `gateway_reference` y
   llamar a `AffiliateService::commissionForSubscription($subscription)`.

## Redsys (recomendado — ya se usa en pasionred)

- Paquete sugerido: `creagia/laravel-redsys` o integración propia con la firma HMAC SHA256.
- Variables `.env`: `REDSYS_MERCHANT_CODE`, `REDSYS_TERMINAL`, `REDSYS_SECRET_KEY`,
  `REDSYS_ENV=live|test`.
- Recuerda (gotcha del `DEPLOY.md`): la URL del webhook de Redsys debe estar **excluida del
  CSRF** — ya contemplado con `webhooks/*`.

## Stripe (alternativa)

- `composer require laravel/cashier` y configurar `STRIPE_KEY` / `STRIPE_SECRET` /
  `STRIPE_WEBHOOK_SECRET`.
- Cashier gestiona suscripciones recurrentes; engancha el evento
  `invoice.payment_succeeded` al mismo `commissionForSubscription()`.

## Renovaciones y comisiones recurrentes

`commissionForSubscription()` es **idempotente por suscripción**. Para comisiones en cada
renovación, crea una nueva fila `Subscription` (o un registro de pago) por periodo cobrado
y vuelve a llamarlo con ese registro.
