-- =========================================================================
-- BASE DE DATOS: CENTRO DE BELLEZA Y SPA 
-- =========================================================================

-- =========================================================================
-- TABLA: roles
-- =========================================================================
CREATE TABLE roles (
    id_rol SERIAL PRIMARY KEY,
    nombre VARCHAR(30) NOT NULL UNIQUE
);

-- =========================================================================
-- TABLA: usuarios
-- =========================================================================
CREATE TABLE usuarios (
    id_usuario SERIAL PRIMARY KEY,
    id_rol INT NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    celular VARCHAR(20) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_usuarios_rol ON usuarios(id_rol);

-- =========================================================================
-- TABLA: empleados
-- =========================================================================
CREATE TABLE empleados (
    id_empleado SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    cargo VARCHAR(80) NOT NULL,
    fecha_ingreso DATE NOT NULL,
    CONSTRAINT fk_empleados_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- =========================================================================
-- TABLA: clientes
-- =========================================================================
CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL UNIQUE,
    CONSTRAINT fk_clientes_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- =========================================================================
-- TABLA: categorias_servicio
-- =========================================================================
CREATE TABLE categorias_servicio (
    id_categoria_servicio SERIAL PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(200) NULL,
    estado SMALLINT NOT NULL DEFAULT 1
);

-- =========================================================================
-- TABLA: servicios
-- =========================================================================
CREATE TABLE servicios (
    id_servicio SERIAL PRIMARY KEY,
    id_categoria_servicio INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL,
    disponibilidad SMALLINT NOT NULL DEFAULT 1,
    imagen VARCHAR(255) NULL,
    CONSTRAINT fk_servicios_categoria
        FOREIGN KEY (id_categoria_servicio) REFERENCES categorias_servicio(id_categoria_servicio)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_servicios_categoria ON servicios(id_categoria_servicio);

-- =========================================================================
-- TABLA: categorias_producto
-- =========================================================================
CREATE TABLE categorias_producto (
    id_categoria_producto SERIAL PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(200) NULL,
    estado SMALLINT NOT NULL DEFAULT 1
);

-- =========================================================================
-- TABLA: productos
-- =========================================================================
CREATE TABLE productos (
    id_producto SERIAL PRIMARY KEY,
    id_categoria_producto INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    disponibilidad SMALLINT NOT NULL DEFAULT 1,
    imagen VARCHAR(255) NULL,
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (id_categoria_producto) REFERENCES categorias_producto(id_categoria_producto)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_productos_categoria ON productos(id_categoria_producto);

-- =========================================================================
-- TABLA: citas
-- =========================================================================
CREATE TABLE citas (
    id_cita SERIAL PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_empleado INT NULL,
    id_servicio INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente' CHECK (estado IN ('Pendiente','Atendida','Cancelada')),
    observacion VARCHAR(255) NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_cliente
        FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_citas_empleado
        FOREIGN KEY (id_empleado) REFERENCES empleados(id_empleado)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_citas_servicio
        FOREIGN KEY (id_servicio) REFERENCES servicios(id_servicio)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_citas_cliente  ON citas(id_cliente);
CREATE INDEX idx_citas_empleado ON citas(id_empleado);
CREATE INDEX idx_citas_servicio ON citas(id_servicio);
CREATE INDEX idx_citas_fecha    ON citas(fecha);

-- =========================================================================
-- TABLA: ventas
-- =========================================================================
CREATE TABLE ventas (
    id_venta SERIAL PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha_venta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    metodo_pago VARCHAR(50) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Completada' CHECK (estado IN ('Completada','Cancelada')),
    observacion VARCHAR(255) NULL,
    CONSTRAINT fk_ventas_cliente
        FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_ventas_cliente ON ventas(id_cliente);

-- =========================================================================
-- TABLA: detalle_venta
-- =========================================================================
CREATE TABLE detalle_venta (
    id_detalle_venta SERIAL PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_venta_venta
        FOREIGN KEY (id_venta) REFERENCES ventas(id_venta)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_venta_producto
        FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_detalle_venta_venta    ON detalle_venta(id_venta);
CREATE INDEX idx_detalle_venta_producto ON detalle_venta(id_producto);

-- =========================================================================
-- TABLA: compras
-- =========================================================================
CREATE TABLE compras (
    id_compra SERIAL PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pagada' CHECK (estado IN ('Pagada','Cancelada')),
    CONSTRAINT fk_compras_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_compras_usuario ON compras(id_usuario);

-- =========================================================================
-- TABLA: detalle_compra
-- =========================================================================
CREATE TABLE detalle_compra (
    id_detalle SERIAL PRIMARY KEY,
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_compra_compra
        FOREIGN KEY (id_compra) REFERENCES compras(id_compra)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_compra_producto
        FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE INDEX idx_detalle_compra_compra    ON detalle_compra(id_compra);
CREATE INDEX idx_detalle_compra_producto  ON detalle_compra(id_producto);

-- =========================================================================
-- SEED: Roles
-- =========================================================================
INSERT INTO roles (nombre) VALUES
    ('Administrador'),
    ('Colaborador'),
    ('Cliente');

-- =========================================================================
-- SEED: Usuario Administrador
-- =========================================================================
INSERT INTO usuarios (id_rol, nombre, apellido, correo, celular, username, password, estado)
VALUES (
    1,
    'Carlos',
    'Mendoza',
    'admin@spabelleza.com',
    '0999999999',
    'admin',
    '$2y$10$XSC0ZW4GCnByfKb4j1xOUeAJjcjh2kxjvP2DofpH6MD1VrV0vUx5y',
    TRUE
);

INSERT INTO empleados (id_usuario, cargo, fecha_ingreso)
VALUES ((SELECT id_usuario FROM usuarios WHERE username = 'admin'), 'Administrador General', '2026-01-15');

-- =========================================================================
-- SEED: Empleados (Colaboradores)
-- =========================================================================
INSERT INTO usuarios (id_rol, nombre, apellido, correo, celular, username, password, estado) VALUES
(2, 'Valeria', 'Rojas',   'valeria.rojas@deluxspa.com',   '0987111222', 'vrojas',   '$2y$10$mgUZBQqTkXSqKMV1lExpbOdGjrwfPzOsAnXdkLWxu/0PUNjKEka5a', TRUE),
(2, 'Daniela', 'Paredes', 'daniela.paredes@deluxspa.com', '0987333444', 'dparedes', '$2y$10$mgUZBQqTkXSqKMV1lExpbOdGjrwfPzOsAnXdkLWxu/0PUNjKEka5a', TRUE),
(2, 'Andrés',  'Salazar', 'andres.salazar@deluxspa.com',  '0987555666', 'asalazar', '$2y$10$mgUZBQqTkXSqKMV1lExpbOdGjrwfPzOsAnXdkLWxu/0PUNjKEka5a', TRUE);

INSERT INTO empleados (id_usuario, cargo, fecha_ingreso) VALUES
((SELECT id_usuario FROM usuarios WHERE username = 'vrojas'),   'Cosmetóloga',             '2026-02-01'),
((SELECT id_usuario FROM usuarios WHERE username = 'dparedes'), 'Estilista',               '2026-02-15'),
((SELECT id_usuario FROM usuarios WHERE username = 'asalazar'), 'Masajista Terapéutico',  '2026-03-01');

-- =========================================================================
-- SEED: Categorías y catálogo
-- =========================================================================
INSERT INTO categorias_producto (nombre_categoria, descripcion, estado) VALUES
('Cabello', 'Shampoo, acondicionadores y tratamientos capilares', 1),
('Facial', 'Productos para limpieza e hidratación facial', 1),
('Corporal', 'Cremas, exfoliantes y aceites corporales', 1),
('Masajes', 'Aceites y productos para terapias de masaje', 1),
('Maquillaje', 'Productos cosméticos y belleza', 1);

INSERT INTO categorias_servicio (nombre_categoria, descripcion, estado) VALUES
('Servicios', 'Servicios generales de spa y belleza', 1),
('Tratamientos', 'Tratamientos especializados de belleza', 1);

INSERT INTO servicios (id_categoria_servicio, nombre, descripcion, precio, disponibilidad) VALUES
(1, 'Manicure clásico', 'Cuidado y esmaltado de uñas de manos', 12.00, 1),
(1, 'Pedicure spa', 'Cuidado completo de pies con exfoliación', 15.00, 1),
(1, 'Corte de cabello', 'Corte y peinado profesional', 10.00, 1),
(1, 'Masaje relajante', 'Masaje corporal de 50 minutos', 35.00, 1),
(2, 'Tratamiento facial hidratante', 'Limpieza profunda e hidratación facial', 25.00, 1),
(2, 'Tratamiento capilar reparador', 'Restauración capilar con keratina', 30.00, 1),
(2, 'Depilación con cera', 'Depilación corporal con cera tibia', 18.00, 1);

INSERT INTO productos (id_categoria_producto, nombre, descripcion, precio, stock, disponibilidad, imagen) VALUES
(1, 'Shampoo Nutritivo 400ml', 'Shampoo con keratina para cabello dañado', 8.50, 40, 1, ''),
(1, 'Acondicionador Reparador 400ml', 'Acondicionador hidratante para cabello seco', 8.50, 35, 1, ''),
(2, 'Crema Facial Hidratante', 'Crema facial con ácido hialurónico', 14.00, 25, 1, ''),
(2, 'Limpiador Facial Espuma', 'Gel limpiador para todo tipo de piel', 9.90, 30, 1, ''),
(3, 'Aceite Corporal Relajante', 'Aceite corporal para masajes', 11.00, 20, 1, ''),
(4, 'Aceite Esencial de Lavanda', 'Aceite esencial para aromaterapia', 7.50, 30, 1, ''),
(5, 'Labial Mate Larga Duración', 'Labial mate en tono rosa nude', 6.00, 50, 1, '');