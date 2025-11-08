<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'mercadolibre_tienda';
    private $username = 'root';
    private $password = '';

    public function getConnection() {
        try {
            $conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $exception) {
            // En producción, evita mostrar errores sensibles
            error_log("Error de conexión: " . $exception->getMessage());
            die("Error al conectar con la base de datos.");
        }
    }
}
?>