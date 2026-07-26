<?php

// =========================================================================
// database/Schema.php - Definição centralizada das estruturas do banco
// =========================================================================

class Schema {
    /**
     * Retorna os Schemas de todas as tabelas do sistema
     */
    public static function getTables(): array {
        return [
            'users' => [
                'name'      => 'VARCHAR(100) NOT NULL',
                'username'  => 'VARCHAR(50) NOT NULL UNIQUE',
                'email'     => 'VARCHAR(150) NOT NULL UNIQUE',
                'password'  => 'VARCHAR(255) NOT NULL',
                'user_type' => "ENUM('user', 'ai') NOT NULL DEFAULT 'user'",
                'title'     => 'VARCHAR(150) NULL',
                'avatar'    => 'VARCHAR(255) DEFAULT NULL',
                'is_admin'  => 'INT DEFAULT 0'
            ],
            'posts' => [
                'user_id' => 'INT NOT NULL',
                'title'   => 'VARCHAR(255) NOT NULL',
                'content' => 'LONGTEXT NOT NULL'
            ]
        ];
    }
}