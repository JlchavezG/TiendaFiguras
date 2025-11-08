
CREATE DATABASE mercadolibre_tienda;
USE mercadolibre_tienda;

-- Tabla de productos
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255),
    stock INT DEFAULT 0
);

-- Tabla de pedidos
CREATE TABLE pedidos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'completado', 'cancelado') DEFAULT 'pendiente'
);

-- Tabla de items del pedido
CREATE TABLE items_pedido (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Insertar productos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, imagen, stock) VALUES
('Laptop Gamer ASUS ROG', 'Laptop gaming con procesador Intel Core i7, 16GB RAM, 1TB SSD y RTX 3060', 1299.00, 'img/imagen.png', 10),
('iPhone 14 Pro', 'Smartphone con cámara profesional, chip A16 Bionic y pantalla Super Retina XDR', 999.00, 'img/iphone.png', 15),
('Audífonos Sony WH-1000XM4', 'Audífonos inalámbricos con cancelación de ruido líder en la industria', 349.00, 'img/audifonos.png', 20),
('Smart TV Samsung 55"', 'Televisor 4K UHD con Quantum HDR y sistema operativo Tizen', 699.00, 'img/tv.png', 8),
('Cámara Canon EOS R5', 'Cámara mirrorless profesional con grabación 8K y estabilización de imagen', 3899.00, 'img/camara.png', 5),
('Bicicleta Trek FX3', 'Bicicleta híbrida de aluminio con cambios Shimano y frenos de disco hidráulicos', 899.00, 'img/bicicleta.png', 12);