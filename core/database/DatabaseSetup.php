<?php
// =========================================================================
// database/DatabaseSetup.php - Gerenciador de inicialização do banco
// =========================================================================

class DatabaseSetup {

    /**
     * Cria ou recria todas as tabelas mapeadas no Schema
     */
    public static function createTables(): bool {
        $conn = DB::getConexao();
        
        if ($conn) {
            $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
        }

        $allCreated = true;
        foreach (Schema::getTables() as $table => $definition) {
            if (!DB::recreateTable($table, $definition)) {
                $allCreated = false;
            }
        }

        if ($conn) {
            $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
        }

        return $allCreated;
    }

    /**
     * Destrói completamente a sessão do usuário
     */
    public static function resetSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_unset();
        session_destroy();
    }
}