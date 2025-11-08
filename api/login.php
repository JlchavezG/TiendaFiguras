<?php
// Solo para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../config/db.php';

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Método no permitido');
}

$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$pass = $_POST['pass'] ?? '';

// Validar campos vacíos
if (empty($email) || empty($pass)) {
    echo '<script>alert("Por favor, completa todos los campos."); window.history.back();</script>';
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $user['Password'] === md5($pass)) {
        // Éxito: iniciar sesión y redirigir al carrito
        $_SESSION['usuario'] = $user['email'];
        header('Location: ../carrito.php');
        exit;
    } else {
        // ❌ Error: mostrar alerta y regresar
        echo '<script>alert("Email o contraseña incorrectos."); window.history.back();</script>';
        exit;
    }
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    echo '<script>alert("Error interno del servidor. Inténtalo más tarde."); window.history.back();</script>';
    exit;
}
?>