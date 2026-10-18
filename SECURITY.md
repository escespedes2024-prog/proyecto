# Seguridad — Actúa Iglesia

Documentación de la postura de seguridad del proyecto, los resultados de los análisis
con **OWASP ZAP** y las correcciones aplicadas.

> Última actualización: 17 de octubre de 2026

---

## 1. Resumen

El proyecto es una aplicación web **Laravel (PHP 8.5) + MySQL/MariaDB** ejecutada de
forma local (Laragon / `php artisan serve`). La autenticación se basa en **roles**
(Administrador, Secretario, Tesorero) y se aplican controles de seguridad en capas
(cabeceras HTTP, *rate limiting*, CSRF, regeneración de sesión, etc.).

Los escaneos **pasivos** con OWASP ZAP no encontraron vulnerabilidades de riesgo
**Alto**. Las incidencias reales de configuración se fueron corrigiendo entre el
primer y el segundo escaneo.

---

## 2. Escaneos OWASP ZAP

| Parámetro | Escaneo 1 | Escaneo 2 |
|-----------|-----------|-----------|
| Herramienta | ZAP by Checkmarx 2.17.0 | ZAP by Checkmarx 2.17.0 |
| Fecha | 17/10/2026 18:09 | 17/10/2026 18:36 |
| Tipo | Pasivo (sin autenticar) | Pasivo (sin autenticar) |
| Sitio | `http://127.0.0.1:8000` | `http://127.0.0.1:8000` |
| Alertas | 8 (1 Medio, 4 Bajos, 3 Info) | 11 (4 Medio, 4 Bajos, 3 Info) |

> ⚠️ **Limitación:** al ser escaneos **pasivos y sin autenticación**, no se probaron
> inyección SQL, XSS, CSRF, control de acceso (IDOR) ni otros fallos activos. Un
> resultado limpio aquí **no certifica** que la aplicación sea segura.

El segundo escaneo **confirma que la cabecera CSP ya se envía** (en el primero no
existía) y, por eso, ahora ZAP evalúa su contenido y señala mejoras en lugar de su
ausencia.

---

## 3. Hallazgos y estado

| # | Alerta | Riesgo | CWE | Estado | Solución |
|---|--------|--------|-----|--------|----------|
| 1 | CSP no configurada | Medio | 693 | **Resuelto** | CSP en middleware **global** |
| 2 | CSP: Failure to Define Directive with No Fallback | Medio | 693 | **Resuelto** | Añadida directiva `form-action 'self'` |
| 3 | CSP: `script-src` unsafe-inline | Medio | 693 | **Riesgo aceptado** | Ver §6.1 |
| 4 | CSP: `style-src` unsafe-inline | Medio | 693 | **Riesgo aceptado** | Ver §6.1 |
| 5 | `X-Powered-By` divulga información | Bajo | 497 | **Resuelto** | `expose_php=Off` + `header_remove()` |
| 6 | Falta `X-Content-Type-Options` (estáticos) | Bajo | 693 | **Parcial** | Middleware (dinámico) + `.htaccess` (Apache) |
| 7 | Cookie sin flag `HttpOnly` | Bajo | 1004 | **Falso positivo** | Es `XSRF-TOKEN`, por diseño (§5.1) |
| 8 | Gran redirección detectada | Bajo | 201 | **Falso positivo** | Cuerpo HTML del 302 de Symfony (§5.2) |
| 9-11 | Aplicación Web Moderna / Petición de Autenticación / Gestión de Sesión | Informativo | — | No aplica | Informativos |

---

## 4. Controles implementados

### 4.1 Cabeceras de seguridad — middleware global

Archivo: `app/Http/Middleware/SecureHeaders.php`, registrado como **middleware global**
(`$middleware->prepend(...)` en `bootstrap/app.php`), de modo que se aplica a **todas
las respuestas**, incluidas las **404 y errores** (que no pasan por el grupo `web`).

- `Content-Security-Policy` — directivas: `default-src`, `img-src`, `style-src`,
  `script-src`, `font-src`, `object-src`, `base-uri`, **`form-action`** y
  `frame-ancestors` (las dos últimas no heredan de `default-src`, por eso se declaran).
- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=()`
- Eliminación de `X-Powered-By` (`remove()` + `header_remove()` a nivel SAPI).

### 4.2 Cabeceras para archivos estáticos

Archivo: `public/.htaccess` (vía `mod_headers` de Apache). Los archivos estáticos
(`/robots.txt`, etc.) **no pasan por Laravel** y se sirven directamente.

```apache
<IfModule mod_headers.c>
    <FilesMatch "\.(txt|xml|css|js|mjs|json|map|png|jpe?g|gif|svg|ico|webp|woff2?|ttf|eot)$">
        Header always set X-Content-Type-Options "nosniff"
        Header always set X-Frame-Options "SAMEORIGIN"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </FilesMatch>
</IfModule>
```

> Aplica solo bajo **Apache (Laragon)**. Con `php artisan serve` los estáticos no
> reciben cabeceras (comportamiento del servidor embebido de PHP).

### 4.3 Configuración de PHP

```ini
expose_php = Off
```

Aplicado en: `C:\php\php.ini`, `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.ini`
y `C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.ini`.
Requiere **reiniciar** el servidor/servicio PHP para tomar efecto.

### 4.4 Autenticación y sesión

- Contraseñas con `bcrypt` (12 rondas) y rehash automático (`Hash::needsRehash`).
- Regeneración de sesión tras login y `invalidate()` + `regenerateToken()` en logout.
- **Rate limiting** en login (5 intentos / 60 s) y en registro (`throttle:register`).
- Cookies de sesión con `HttpOnly` y `SameSite=Lax`; cifrado de sesión (`SESSION_ENCRYPT=true`).
- Middleware de roles (`role:...`) y `prevent-back-history`.

---

## 5. Falsos positivos documentados

### 5.1 Cookie sin flag `HttpOnly`
Corresponde al `XSRF-TOKEN`, que **no debe** llevar `HttpOnly` (el JavaScript lo lee
para el CSRF). La cookie de sesión **sí** es `HttpOnly`. → marcar *False Positive*.

### 5.2 Gran redirección detectada (`POST /login`)
El `302` incluye el HTML por defecto de Symfony ("Redirecting to …"); como el cuerpo
(~354 B) supera la longitud del `Location` (~31 car.), ZAP lo señala. No hay datos
sensibles (la contraseña nunca se refleja). → marcar *False Positive*.

---

## 6. Riesgos aceptados y pendientes

### 6.1 CSP con `unsafe-inline` (aceptado por ahora)
Las vistas usan bloques `<script>` en línea **y** manejadores en línea
(`onclick="..."`), además de atributos `style="..."`. Quitar `'unsafe-inline'`
requiere refactorizar las vistas (mover el JS a archivos y usar **nonces** +
`addEventListener`), por lo que se acepta como riesgo controlado.

**Plan de endurecimiento:** migrar a CSP basada en *nonces*
(`spatie/laravel-csp` o `Vite::useCspNonce()`), eliminar manejadores/estilos en línea
y luego retirar `'unsafe-inline'`.

### 6.2 Archivos estáticos bajo `php artisan serve`
`/robots.txt` no recibe cabeceras con el servidor embebido. En **producción** se sirve
bajo Apache/Nginx, donde `.htaccess` (o la config del servidor) sí las aplica.

---

## 7. Recomendaciones para producción

- [ ] `APP_ENV=production`, `APP_DEBUG=false`.
- [ ] **HTTPS** obligatorio + `SESSION_SECURE_COOKIE=true`; activar **HSTS**.
- [ ] Servir con **Apache/Nginx** (no `php artisan serve`) para cubrir estáticos.
- [ ] **Endurecer la CSP** (nonces, sin `unsafe-inline`).
- [ ] **Re-escaneo con ZAP activo y autenticado** (un usuario por rol).
- [ ] Reiniciar el servicio PHP tras cambios de `php.ini`.

---

## 8. Reportar una vulnerabilidad

Repórtala de forma privada al responsable del proyecto, incluyendo: descripción,
pasos para reproducir, impacto y, si es posible, una propuesta de solución. No la
publiques en un issue público.
