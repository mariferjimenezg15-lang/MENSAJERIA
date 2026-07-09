# Mensajería Móvil

Aplicación web en **PHP + MySQL** para gestionar clientes, teléfonos móviles
(saldo, estado, desvío) y el envío/historial de mensajes.

## Requisitos
- PHP 8.0 o superior con extensión **PDO MySQL**
- Servidor MySQL / MariaDB
- Servidor web (Apache, Nginx) o el servidor embebido de PHP para pruebas

## Instalación

1. **Crear la base de datos**
   Importa el archivo `schema.sql` en tu servidor MySQL:
   ```bash
   mysql -u root -p < schema.sql
   ```

2. **Configurar la conexión**
   Edita `config.php` y coloca tus credenciales:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'mensajeria_movil');
   define('DB_USER', 'tu_usuario');
   define('DB_PASS', 'tu_password');
   ```

3. **Crear el primer usuario administrador**
   Abre en el navegador (o ejecuta por consola) `crear_admin.php` **una sola vez**:
   ```
   http://localhost/mensajeria-movil/crear_admin.php
   ```
   Esto crea la cuenta `admin` / `admin123` (puedes cambiar estos valores dentro
   del archivo antes de ejecutarlo). **Borra `crear_admin.php` después de usarlo.**

4. **Probar la aplicación**
   Si usas el servidor embebido de PHP para pruebas rápidas:
   ```bash
   php -S localhost:8000
   ```
   Y abre `http://localhost:8000/login.php`

## Estructura del proyecto

```
mensajeria-movil/
├── config.php                 # Conexión PDO a MySQL
├── schema.sql                 # Esquema de la base de datos
├── crear_admin.php             # Script de creación del primer admin (borrar tras usar)
├── index.php                  # Redirección según sesión
├── login.php / login_process.php
├── register.php / register_process.php
├── logout.php
├── dashboard.php               # Inicio del cliente (resumen)
├── mis_telefonos.php           # Alta / baja de teléfonos
├── telefono.php                # Encender/apagar, recargar saldo, desvío
├── enviar_mensaje.php / enviar_mensaje_process.php
├── historial.php                # Historial de mensajes del cliente
├── includes/
│   ├── auth.php                 # Control de sesión y roles
│   ├── header.php / footer.php  # Layout cliente
│   └── header_admin.php / footer_admin.php  # Layout admin
├── admin/
│   ├── dashboard.php             # Resumen global
│   ├── usuarios.php              # Gestión de clientes
│   ├── telefonos.php             # Gestión de todos los teléfonos
│   └── mensajes.php              # Historial global de mensajes
└── assets/style.css
```

## Lógica de negocio

- **Costo por mensaje:** $1.00 (configurable en `config.php`, constante `COSTO_MENSAJE`).
- **Envío de mensajes:** el teléfono de origen debe estar **conectado** y tener
  saldo suficiente; al enviarse, se descuenta el saldo automáticamente.
- **Desvío:** si el número destino tiene un desvío activo, el sistema sigue la
  cadena de desvíos hasta encontrar el número final (`entregado_a`), con
  protección contra bucles infinitos.
- **Estado del mensaje:**
  - `entregado`: el número final está registrado y conectado (o es un número
    externo al sistema).
  - `pendiente`: el número final está registrado pero desconectado.
- **Roles:** `cliente` y `admin`, controlados en la tabla `usuarios` y validados
  en cada página mediante `requiereRol()`.

## Seguridad implementada
- Contraseñas con `password_hash()` / `password_verify()`.
- Consultas SQL preparadas (PDO) en toda la aplicación — sin concatenación de
  variables en SQL.
- Escape de salida con `htmlspecialchars()` para evitar XSS.
- Verificación de pertenencia (un cliente solo puede administrar sus propios
  teléfonos y ver su propio historial).
