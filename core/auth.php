<?php

function isAdmin() {
    $conn = DB::getConexao();

    // Garante que a sessão esteja ativa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Se não estiver logado, retorna false imediatamente
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $userId = $_SESSION['user_id'];

    // Prepara a consulta para evitar SQL Injection
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ? LIMIT 1");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Retorna true se encontrar o usuário e is_admin for 1
    return ($result && (int)$result['is_admin'] === 1);
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: auth.php");
        exit;
    }
}
function checkLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        header("Location: " . url('/'));
        exit;
    }
}