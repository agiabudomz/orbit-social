<?php

Router::add('GET', '/api/posts', function() {
    $conn = DB::getConexao();

    // Parametros de paginacao
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 5;
    $offset = ($page - 1) * $limit;

    // Busca o total para calcular se ha mais paginas
    $totalQuery = $conn->query("SELECT COUNT(*) AS total FROM posts");
    $totalRows = $totalQuery ? (int)$totalQuery->fetch_assoc()['total'] : 0;
    $hasMore = ($offset + $limit) < $totalRows;

    // Consulta paginada com Prepared Statement (Protecao contra SQL Injection)
    $stmt = $conn->prepare("SELECT
            posts.id,
            posts.title,
            posts.content,
            posts.created_at,
            users.name AS author_name,
            users.username AS author_username,
            users.title AS author_title,
            users.avatar AS author_avatar
        FROM posts
        INNER JOIN users ON posts.user_id = users.id
        ORDER BY posts.id DESC
        LIMIT ? OFFSET ?");

    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }

    jsonResponse(true, "Postagens recuperadas com sucesso.", [
        'posts'      => $posts,
        'page'       => $page,
        'has_more'   => $hasMore,
        'total'      => $totalRows,
        'isLoggedIn' => Auth::check()
    ]);
});


Router::add('POST', '/api/newpost', function() {

    // 1. Verificação de Autenticação
    if (!Auth::check()) {
        jsonResponse(false, "Você precisa estar logado para criar uma publicação.");
        exit;
    }

    // Obtém o ID do usuário autenticado
    $currentUser = Auth::user();
    $userId = $currentUser->id ?? $_SESSION['user_id'] ?? null;

    if (!$userId) {
        jsonResponse(false, "Sessão inválida. Por favor, faça login novamente.");
        exit;
    }

    // 2. Captura dos Dados (Suporta FormData de formulário e Payload JSON)
    $data = $_POST;
    if (empty($data)) {
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true) ?? [];
    }

    $title   = trim($data['title'] ?? '');
    $content = trim($data['content'] ?? '');

    // 3. Validação dos Campos Obrigatórios
    if (empty($title) || empty($content)) {
        jsonResponse(false, "O título e o conteúdo da publicação são obrigatórios.");
        exit;
    }

    // 4. Validação de Tamanho (VARCHAR 255 na tabela)
    if (mb_strlen($title) > 255) {
        jsonResponse(false, "O título não pode exceder 255 caracteres.");
        exit;
    }

    // Sanitização leve para o título
    $titleSanitized = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    // 5. Inserção no Banco de Dados
    $conn = DB::getConexao();

    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $titleSanitized, $content);

    if ($stmt->execute()) {
        $postId = $stmt->insert_id;

        jsonResponse(true, "Publicação criada com sucesso!", [
            'post' => [
                'id'         => $postId,
                'user_id'    => $userId,
                'title'      => $titleSanitized,
                'content'    => $content,
                'created_at' => date('Y-m-d H:i:s')
            ],
            'redirect' => url('/feed')
        ]);
    } else {
        jsonResponse(false, "Erro ao salvar a publicação no banco de dados.");
    }
});
