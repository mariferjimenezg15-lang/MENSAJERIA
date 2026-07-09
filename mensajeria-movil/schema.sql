-- ============================================================
-- Mensajería Móvil - Esquema de Base de Datos MySQL
-- ============================================================
CREATE DATABASE IF NOT EXISTS mensajeria_movil
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mensajeria_movil;

-- ------------------------------------------------------------
-- Tabla: usuarios (clientes y administradores)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nif VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabla: telefonos (números móviles asociados a un cliente)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS telefonos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    numero VARCHAR(10) NOT NULL UNIQUE,
    saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('conectado','desconectado') NOT NULL DEFAULT 'desconectado',
    desvio_numero VARCHAR(10) DEFAULT NULL,
    fecha_alta DATE NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tabla: mensajes (historial de mensajes enviados)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telefono_origen_id INT DEFAULT NULL,
    numero_origen VARCHAR(10) NOT NULL,
    numero_destino VARCHAR(10) NOT NULL,
    entregado_a VARCHAR(10) DEFAULT NULL,
    mensaje VARCHAR(150) NOT NULL,
    costo DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    estado ENUM('entregado','pendiente','fallido') NOT NULL DEFAULT 'pendiente',
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (telefono_origen_id) REFERENCES telefonos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- NOTA: no se inserta aquí ningún usuario administrador porque la
-- contraseña debe quedar guardada con password_hash() de PHP.
-- Después de crear la base de datos, ejecuta una sola vez el
-- script "crear_admin.php" (incluido en el proyecto) para crear
-- tu primer usuario administrador de forma segura, y luego bórralo.
-- ------------------------------------------------------------
