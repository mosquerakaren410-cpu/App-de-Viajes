# Aplicación Web de Viajes

Proyecto Fullstack desarrollado para un proceso de selección de desarrollador Fullstack.

## Tecnologías

- **Frontend:** Angular 22 + Bootstrap 5 + ngx-translate.
- **Backend:** Laravel 13 + PostgreSQL.
- **Autenticación:** JWT con access token + refresh token.
- **Pruebas:** PHPUnit.
- **Pruebas de API:** Postman.

---

# 1. Instalación

## Requisitos previos

Se necesita tener instalado:

- PHP 8.3 o superior.
- Composer.
- Node.js y npm.
- PostgreSQL.
- Angular CLI compatible con el proyecto.
- Postman, si se desea ejecutar la colección de pruebas de API.

## 1.1 Backend

Ubicarse en la carpeta:

```powershell
cd backend/laravel
```

Instalar las dependencias:

```powershell
composer install
```

Copiar el archivo de configuración de entorno:

```powershell
copy .env.example .env
```

Configurar en `.env` la conexión a PostgreSQL. Ejemplo:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=viajes_app
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

Generar la clave de Laravel:

```powershell
php artisan key:generate
```

Después de configurar el secreto de tokens (ver sección 2), ejecutar las migraciones y los datos iniciales:

```powershell
php artisan migrate
php artisan db:seed
```

Iniciar el backend:

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

El backend quedará disponible en:

```text
http://localhost:8000
```

> No subir el archivo `.env` al repositorio. Contiene secretos y credenciales.

---

## 1.2 Frontend

Ubicarse en:

```powershell
cd frontend/App
```

Instalar dependencias:

```powershell
npm install
```

Iniciar Angular:

```powershell
npm start
```

El frontend utiliza el proxy configurado para enviar las peticiones `/api` al backend Laravel.

Por defecto se puede acceder desde:

```text
http://localhost:4200
```

---

# 2. Generación del secreto para los tokens

La aplicación utiliza una variable independiente llamada `APP_TOKEN_SECRET` para generar las claves utilizadas en la autenticación.

Para generar un secreto criptográficamente aleatorio de 32 bytes:

```powershell
php -r "echo base64_encode(random_bytes(32)), PHP_EOL;"
```

El resultado se debe guardar en el archivo `.env`:

```env
APP_TOKEN_SECRET=PEGA_AQUI_EL_RESULTADO
```

No se debe publicar este valor en GitHub, Postman, capturas de pantalla ni documentación pública.

---

# 3. Librería JWT utilizada

La aplicación utiliza:

```text
firebase/php-jwt
```

Versión utilizada en el proyecto:

```text
^7.2
```

La dependencia se encuentra declarada en `composer.json`.

Se escogió `firebase/php-jwt` porque permite generar y validar tokens JWT de forma explícita desde el backend, manteniendo bajo control la creación del payload, la firma, la expiración y la validación del token.

En `TokenService` se utiliza el algoritmo:

```text
HS256
```

El flujo implementado es:

1. Se obtiene el secreto configurado en `APP_TOKEN_SECRET`.
2. Se derivan claves independientes mediante HKDF-SHA256.
3. El JWT se firma con HS256.
4. El JWT se cifra antes de entregarlo al cliente utilizando AES-256-GCM.
5. El backend descifra y valida el token cuando recibe una petición protegida.

El access token contiene, entre otros datos:

- `sub`: identificador del usuario.
- `iat`: fecha/hora de emisión.
- `exp`: fecha/hora de expiración.
- `jti`: identificador único del token.
- `idioma`: idioma del usuario.

El access token tiene una duración de 15 minutos.

El refresh token es independiente del JWT y se genera como un valor aleatorio de alta entropía. Se utiliza para obtener nuevos access tokens y se controla su reutilización y revocación.

---

# 4. ¿Dónde se guardan los tokens?

El frontend guarda el access token y el refresh token en:

```text
sessionStorage
```

Se utiliza `sessionStorage` porque los tokens se eliminan al cerrar la pestaña o sesión del navegador y no permanecen como almacenamiento persistente.

Aun así, `sessionStorage` es accesible desde JavaScript. Por esta razón, la aplicación debe protegerse frente a XSS y no debe almacenar allí información sensible que no sea necesaria.

---

# 5. Flujo de autenticación

El flujo completo de autenticación es:

```text
                         LOGIN
                           |
                           v
                POST /api/auth/login
                           |
                           v
              +-------------------------+
              | access_token            |
              | refresh_token           |
              +-------------------------+
                           |
                           v
                    sessionStorage
                           |
                           v
                PETICIÓN PROTEGIDA
                           |
                    +------+------+
                    |             |
                   200          401
                    |             |
                    |       AUTH_TOKEN_EXPIRED
                    |             |
                    |             v
                    |    POST /api/auth/refresh
                    |             |
                    |       +-----+-----+
                    |       |           |
                    |      200         401
                    |       |           |
                    |       v           v
                    |  nuevos tokens   LOGIN
                    |       |
                    |       v
                    |  repetir petición
                    |
                    v
                 RESPUESTA


                         LOGOUT
                           |
                           v
                POST /api/auth/logout
                           |
                           v
              +-------------------------+
              | Revoca access token     |
              | Revoca refresh tokens   |
              +-------------------------+
                           |
                           v
                    Sesión cerrada
```

## 5.1 Login

El usuario envía sus credenciales a:

```http
POST /api/auth/login
```

Si las credenciales son correctas, el backend devuelve un access token y un refresh token.

El frontend almacena ambos en `sessionStorage`.

---

## 5.2 Petición protegida

Para acceder a endpoints protegidos, Angular agrega el access token mediante el `HttpInterceptor`:

```http
Authorization: Bearer <access_token>
```

El backend valida el token antes de procesar la petición.

---

## 5.3 Refresh automático

Si una petición protegida responde:

```text
401 AUTH_TOKEN_EXPIRED
```

el interceptor realiza una única llamada a:

```http
POST /api/auth/refresh
```

Si el refresh es válido:

1. El backend genera nuevos tokens.
2. Angular actualiza `sessionStorage`.
3. Se repite la petición original automáticamente.

Si el refresh falla, la sesión se cierra y el usuario debe volver al login.

El flujo evita realizar múltiples llamadas de refresh para la misma expiración.

---

## 5.4 Logout

El cierre de sesión se realiza mediante:

```http
POST /api/auth/logout
```

Al cerrar sesión se revoca el `jti` del access token y se revocan los refresh tokens asociados al usuario.

Después del logout, un token revocado no puede utilizarse nuevamente.

---

# 6. Endpoints principales

## Autenticación

```text
POST /api/auth/register
POST /api/auth/login
POST /api/auth/refresh
POST /api/auth/logout
```

## Países y ciudades

```text
GET /api/paises
GET /api/paises/{id}/ciudades
```

## Consultas

```text
POST /api/consultas
GET /api/consultas/historial
```

---

# 7. Funcionalidades del frontend

La aplicación incluye:

1. Registro de usuario.
2. Inicio de sesión.
3. Selección de país.
4. Selección de ciudad.
5. Presupuesto en COP.
6. Consulta de clima actual.
7. Consulta de moneda y tasa de cambio.
8. Conversión del presupuesto.
9. Historial de las últimas 5 consultas.
10. Navegación Atrás/Siguiente.
11. Volver al inicio.
12. Cambio de idioma entre Español y Alemán.
13. `AuthGuard` para rutas protegidas.
14. `HttpInterceptor` para autenticación y refresh automático.
15. Mensajes amigables para errores.
16. Diseño responsive con Bootstrap 5.

Los países incluidos son:

- Inglaterra.
- Japón.
- India.
- Dinamarca.

Cada país cuenta con dos ciudades.

---

# 8. APIs externas

La aplicación utiliza dos APIs externas:

## Clima — OpenWeatherMap

Se utiliza:

```text
https://api.openweathermap.org/data/2.5/weather
```

Permite obtener la temperatura actual de la ciudad seleccionada.

La consulta utiliza unidades métricas para obtener la temperatura en grados Celsius.

La clave se configura mediante:

```env
WEATHER_API_KEY=...
```

## Moneda — ExchangeRate-API

Se utiliza:

```text
https://v6.exchangerate-api.com/v6/{API_KEY}/latest/COP
```

La aplicación consulta las tasas de cambio tomando COP como moneda de origen.

La clave se configura mediante:

```env
EXCHANGE_API_KEY=...
```

Las tasas obtenidas correctamente se guardan en la tabla `tasas_cambio`.

Si ExchangeRate-API no está disponible o devuelve una respuesta no válida, `MonedaServices` intenta utilizar la última tasa almacenada para la moneda solicitada.

Esto permite mantener disponible la conversión cuando existe una tasa previamente guardada.

---

# 9. Pruebas PHPUnit

Las pruebas se encuentran en:

```text
backend/laravel/tests/Feature/AuthTest.php
backend/laravel/tests/Feature/ConsultaTest.php
backend/laravel/tests/Unit/MonedaServicesTest.php
```

Se cubren, entre otros, estos escenarios:

- Token válido.
- Token alterado.
- Token firmado con otro secreto.
- Token vencido.
- Token revocado después del logout.
- Reutilización de refresh token.
- Límite de intentos de login.
- Presupuesto inválido.
- Conversión de moneda con mock.
- Uso de la última tasa guardada cuando falla la API externa.

Ejecutar:

```powershell
cd backend/laravel
php artisan test
```

Resultado esperado del proyecto:

```text
12 tests passed
52 assertions
```

---

# 10. Colección Postman

La colección de Postman permite probar la API sin copiar tokens manualmente.

Archivos:

```text
postman/Viajes_App.postman_collection.json
postman/Viajes_App.postman_environment.json
```

La colección está organizada en:

- Autenticación.
- Países y ciudades.
- Consultas.
- APIs externas.
- Casos de error.

El login guarda automáticamente:

```text
access_token
refresh_token
```

en las variables del entorno de Postman.

También se prueban casos como:

- Sin token.
- Token alterado.
- Token vencido.
- Presupuesto vacío.
- Tipo de dato inválido.
- Login incorrecto.
- Refresh.
- Logout.
- Uso de token después del logout.

---

# 11. Seguridad

La implementación contempla:

- JWT firmado con HS256.
- Expiración del access token.
- Refresh token independiente.
- Rotación y revocación de tokens.
- Identificador `jti` para revocación del access token.
- Protección de endpoints mediante middleware.
- `AuthGuard` en Angular.
- `HttpInterceptor` para enviar Bearer tokens.
- Refresh automático ante `AUTH_TOKEN_EXPIRED`.
- Límite de intentos de login.
- No exposición del JSON crudo del backend al usuario.
- Secretos almacenados mediante variables de entorno.

No se deben subir al repositorio:

```text
.env
APP_TOKEN_SECRET
WEATHER_API_KEY
EXCHANGE_API_KEY
contraseñas
tokens
credenciales de PostgreSQL
```

---

# 12. Estructura general

```text
Viajes_App/
│
├── backend/
│   └── laravel/
│       ├── app/
│       │   ├── Http/
│       │   ├── Models/
│       │   └── Services/
│       ├── database/
│       ├── routes/
│       ├── tests/
│       └── postman/
│
└── frontend/
    └── App/
        └── src/
            └── app/
```

---

# 13. Resumen del flujo completo

```text
Usuario
   |
   v
Angular
   |
   | login
   v
Laravel
   |
   | access token + refresh token
   v
sessionStorage
   |
   | petición protegida
   v
Laravel
   |
   +---- 200 ----------------------> Angular
   |
   +---- 401 AUTH_TOKEN_EXPIRED
                 |
                 v
          /api/auth/refresh
                 |
           +-----+-----+
           |           |
          200         401
           |           |
           v           v
     nuevos tokens    Login
           |
           v
    repetir petición
           |
           v
         Angular

Logout
   |
   v
/api/auth/logout
   |
   v
Revocación de tokens
   |
   v
Sesión cerrada
```

---

## ¿Con que fue creado?

Este proyecto utiliza Laravel + PostgreSQL en el backend y Angular + Bootstrap 5 en el frontend. La autenticación combina access tokens JWT de corta duración con refresh tokens, y el frontend gestiona automáticamente la renovación cuando el access token expira.

Las APIs externas utilizadas son OpenWeatherMap para clima y ExchangeRate-API para tasas de cambio.
