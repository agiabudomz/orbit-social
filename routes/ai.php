<?php

Router::add('GET', '/api/ai/test-generate', function() {
    
    // 1. Captura os parâmetros via Query String ($_GET)
    $nicho      = trim($_GET['nicho'] ?? $_GET['tema'] ?? 'Empreendedorismo e Negócios');
    $quantidade = isset($_GET['quantidade']) ? max(1, min((int)$_GET['quantidade'], 5)) : 2;
    $tomDeVoz   = trim($_GET['tom'] ?? $_GET['tom_de_voz'] ?? 'engajador');

    // 2. Sanitização dos dados
    $nicho      = htmlspecialchars($nicho, ENT_QUOTES, 'UTF-8');
    $tomDeVoz   = htmlspecialchars($tomDeVoz, ENT_QUOTES, 'UTF-8');

    // 3. Chave da API (Defina no seu config/env)
    $geminiKey  = $_ENV['GEMINI_AI_KEY'];
   
    if (empty($geminiKey)) {
        jsonResponse(false, "Chave de API da Gemini (GEMINI_API_KEY) não configurada no servidor.");
        exit;
    }

    // 4. Monta o Prompt e Chama a API Gemini
    $prompt = generate_ai_prompt($nicho, $quantidade, $tomDeVoz);
    $resultadoIA   = call_gemini_api($prompt, $geminiKey);
    die(var_dump($resultadoIA));

    if (!$resultadoIA || empty($resultadoIA['posts'])) {
        jsonResponse(false, "Falha ao gerar o conteúdo pela IA. Verifique a chave ou tente outro nicho.");
        exit;
    }

    // 5. Simulação do Horário Humanizado (Apenas Exibição / Sem Gravar no Banco)
    $postsSimulados = [];
    $diasEspacados  = 0;

    foreach ($resultadoIA['posts'] as $post) {
        $title   = trim($post['title'] ?? '');
        $content = trim($post['content'] ?? '');

        if (empty($title) || empty($content)) continue;

        $postsSimulados[] = [
            'title'      => $title,
            'content'    => $content,
            'created_at' => generate_post_time('now', -$diasEspacados),
            'simulated'  => true
        ];

        $diasEspacados++;
    }

    // 6. Retorna a resposta limpa em JSON na tela
    jsonResponse(true, "Teste realizado com sucesso via GET!", [
        'parametros' => [
            'nicho'      => $nicho,
            'quantidade' => $quantidade,
            'tom_de_voz' => $tomDeVoz
        ],
        'count'         => count($postsSimulados),
        'posts_gerados' => $postsSimulados
    ]);
});


Router::add('POST', '/api/ai/generate', function() {
    
    // 1. Verificação de Autenticação
    if (!Auth::check()) {
        jsonResponse(false, "Você precisa estar logado para gerar publicações automáticas.");
        exit;
    }

    $currentUser = Auth::user();
    $userId      = $currentUser->id ?? $_SESSION['user_id'] ?? null;

    if (!$userId) {
        jsonResponse(false, "Sessão inválida. Por favor, refaça o login.");
        exit;
    }

    // 2. Captura de Dados (FormData e JSON)
    $data = $_POST;
    if (empty($data)) {
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true) ?? [];
    }

    // 3. Validação do Campo Obrigatório
    $nicho = trim($data['nicho'] ?? $data['tema'] ?? '');
    if (empty($nicho)) {
        jsonResponse(false, "Por favor, informe o nicho ou tema da publicação.");
        exit;
    }

    // 4. Sanitização dos Dados
    $nicho      = htmlspecialchars($nicho, ENT_QUOTES, 'UTF-8');
    $quantidade = isset($data['quantidade']) ? max(1, min((int)$data['quantidade'], 10)) : 1;
    $tomDeVoz   = !empty($data['tom_de_voz']) ? htmlspecialchars(trim($data['tom_de_voz']), ENT_QUOTES, 'UTF-8') : 'engajador';

    $geminiKey  = $_ENV['GEMINI_AI_KEY'];

    if (empty($geminiKey)) {
        jsonResponse(false, "Configuração de API pendente no servidor.");
        exit;
    }

    // 5. Geração de Conteúdo via IA
    $prompt = generate_ai_prompt($nicho, $quantidade, $tomDeVoz);
    $resultadoIA   = call_gemini_api($prompt, $geminiKey);

    if (!$resultadoIA || empty($resultadoIA['posts'])) {
        jsonResponse(false, "Não foi possível gerar os posts no momento. Tente novamente.");
        exit;
    }

    // 6. Inserção no Banco de Dados com Horário Humanizado
    $conn = DB::getConexao();
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, ?)");

    $postsSalvos   = [];
    $diasEspacados  = 0;

    foreach ($resultadoIA['posts'] as $post) {
        $title   = trim($post['title'] ?? '');
        $content = trim($post['content'] ?? '');

        if (empty($title) || empty($content)) continue;

        // Gera timestamp orgânico/humano
        $createdAt = generate_post_time('now', -$diasEspacados);

        $stmt->bind_param("isss", $userId, $title, $content, $createdAt);

        if ($stmt->execute()) {
            $postsSalvos[] = [
                'id'         => $stmt->insert_id,
                'user_id'    => $userId,
                'title'      => $title,
                'created_at' => $createdAt
            ];
        }

        $diasEspacados++;
    }

    // 7. Retorno de Sucesso
    if (!empty($postsSalvos)) {
        jsonResponse(true, count($postsSalvos) . " publicação(ões) criada(s) com sucesso!", [
            'posts'    => $postsSalvos,
            'redirect' => url('/feed')
        ]);
    } else {
        jsonResponse(false, "Ocorreu um erro ao gravar as publicações no banco de dados.");
    }
});


Router::add('POST', '/api/ai/test-generate', function() {
    
    // 1. Captura de Dados (Suporta $_POST e php://input JSON)
    $data = $_POST;
    if (empty($data)) {
        $jsonInput = file_get_contents('php://input');
        $data = json_decode($jsonInput, true) ?? [];
    }

    // 2. Verificação de Campos Obrigatórios
    $nicho = trim($data['nicho'] ?? $data['tema'] ?? '');
    if (empty($nicho)) {
        jsonResponse(false, "O campo 'nicho' (ou 'tema') é obrigatório para testar a IA.");
        exit;
    }

    // 3. Sanitização e Configuração dos Parâmetros
    $nicho      = htmlspecialchars($nicho, ENT_QUOTES, 'UTF-8');
    $quantidade = isset($data['quantidade']) ? max(1, min((int)$data['quantidade'], 5)) : 1;
    $tomDeVoz   = !empty($data['tom_de_voz']) ? htmlspecialchars(trim($data['tom_de_voz']), ENT_QUOTES, 'UTF-8') : 'engajador';
    
    // Chave da API (Defina no seu config/env)

    $geminiKey  = $_ENV['GEMINI_AI_KEY'];

    if (empty($geminiKey)) {
        jsonResponse(false, "Chave de API da Gemini (GEMINI_API_KEY) não configurada no servidor.");
        exit;
    }

    // 4. Monta o Prompt e Chama a API Gemini
    $prompt = generate_ai_prompt($nicho, $quantidade, $tomDeVoz);
    $resultadoIA   = call_gemini_api($prompt, $geminiKey);
    

    if (!$resultadoIA || empty($resultadoIA['posts'])) {
        jsonResponse(false, "Falha ao gerar o conteúdo pela IA. Verifique a chave ou tente outro nicho.");
        exit;
    }

    // 5. Simulação do Horário Humanizado (Sem Registrar no Banco)
    $postsSimulados = [];
    $diasEspacados  = 0;

    foreach ($resultadoIA['posts'] as $post) {
        $title   = trim($post['title'] ?? '');
        $content = trim($post['content'] ?? '');

        if (empty($title) || empty($content)) continue;

        // Atribui uma data humanizada sem salvar na DB
        $postsSimulados[] = [
            'title'      => $title,
            'content'    => $content,
            'created_at' => generate_post_time('now', -$diasEspacados),
            'simulated'  => true
        ];

        $diasEspacados++;
    }

    // 6. Retorna Apenas a Resposta em JSON
    jsonResponse(true, "Publicações simuladas com sucesso! (Modo de Teste)", [
        'nicho'          => $nicho,
        'tom_de_voz'     => $tomDeVoz,
        'count'          => count($postsSimulados),
        'posts_gerados'  => $postsSimulados
    ]);
});