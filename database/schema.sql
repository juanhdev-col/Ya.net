-- TABLAS PRINCIPALES

CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS areas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    area_id INT REFERENCES areas(id) ON DELETE SET NULL,
    rol_id INT REFERENCES roles(id) ON DELETE SET NULL,
    fecha_nacimiento DATE,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS noticias (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    resumen TEXT,
    contenido TEXT,
    imagen VARCHAR(255),
    destacada BOOLEAN DEFAULT FALSE,
    publicado_por INT REFERENCES usuarios(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS aplicativos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    url_acceso VARCHAR(255) NOT NULL,
    icono VARCHAR(50),
    orden INT DEFAULT 0
);

-- INSERCIÃ“N DE DATOS INICIALES (SEEDS)

INSERT INTO roles (nombre) VALUES ('Administrador'), ('Colaborador');
INSERT INTO areas (nombre) VALUES ('TecnologÃ­a'), ('Control Interno'), ('GestiÃ³n Humana');

-- Usuario Admin por defecto (ContraseÃ±a: admin123)
INSERT INTO usuarios (nombre, apellido, email, password, area_id, rol_id, fecha_nacimiento) 
VALUES ('Admin', 'Yajuego', 'admin@yajuego.co', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe112I1S.95m5z32iR1g0J9Qn9x5.n2C6', 1, 1, '1990-01-01');

INSERT INTO aplicativos (nombre, url_acceso, icono, orden) VALUES 
('BUK', 'https://buk.co', 'BUK', 1),
('Mesa de Ayuda', '#', 'TI', 2),
('CRM', '#', 'CRM', 3),
('Correo', '#', 'MAIL', 4);
-- CatÃ¡logo corporativo de GestiÃ³n Documental. En bases existentes, aplicar ademÃ¡s
-- la migraciÃ³n database/migrations_sgc_documents.sql para aÃ±adir auditorÃ­a.
CREATE TABLE IF NOT EXISTS departments (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS sgc_documents (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    version INT NOT NULL DEFAULT 1,
    doc_type ENUM('politica','manual','procedimiento','instructivo','protocolo','guia','matriz','informe','formato') NOT NULL,
    department_id INT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_downloadable TINYINT(1) NOT NULL DEFAULT 0,
    valid_from DATE NULL,
    status ENUM('active','archived') NOT NULL DEFAULT 'active',
    archived_at DATETIME NULL,
    archived_by INT NULL,
    archive_reason VARCHAR(500) NULL,
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sgc_documents_status (status),
    INDEX idx_sgc_documents_department (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


