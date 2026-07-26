<?php

// =========================================================================
// routes/web.php - Rotas de Administração e Manutenção do Banco
// =========================================================================

// Cria/Garante a estrutura das tabelas no banco de dados
Router::add('GET', 'createdb', function() {
    $status = DatabaseSetup::createTables();
    echo $status ? "Banco de dados criado com sucesso!" : "Erro ao criar o banco de dados.";
});

// Alteração pontual de colunas
Router::add('GET', 'alterdb', function() {
    $success = DB::alterTable('users', 'ADD', 'is_admin', 'INT DEFAULT 0');
    echo $success ? "Banco de dados alterado com sucesso!" : "Erro ao alterar banco de dados ou coluna já existente.";
});

// Reconstroi o ambiente do zero: limpa sessão e recria tabelas
Router::add('GET', 'rebuilddb', function() {
    DatabaseSetup::resetSession();
    
    $status = DatabaseSetup::createTables();
    echo $status 
        ? "Sessão resetada e Banco de Dados recriado com sucesso!" 
        : "Erro ao recriar o banco de dados.";
});

// Reseta apenas os dados da tabela mantendo a estrutura
Router::add('GET', 'reset-users', function() {
    $status = DB::resetTable('users');
    echo $status ? "Tabela 'users' limpa com sucesso!" : "Erro ao resetar os registros da tabela 'users'.";
});