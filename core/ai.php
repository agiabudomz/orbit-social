<?php

/**
 * Executa requisição para o endpoint de Interactions do Gemini.
 */
/**
 * Executa a requisição cURL para a API de Interactions do Gemini
 * e extrai o JSON gerado convertendo para um Array PHP.
 */
function call_gemini_api(string $prompt, string $apiKey, string $model = 'gemini-3.6-flash'): ?array 
{
    $url = "https://generativelanguage.googleapis.com/v1beta/interactions";

    $payload = [
        'model' => $model,
        'input' => $prompt
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_TIMEOUT        => 30
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error || !$response) {
        return null;
    }

    $data = json_decode($response, true);

    // 1. Procura a etapa que contém a saída da IA (model_output)
    $textoJSON = null;
    if (isset($data['steps']) && is_array($data['steps'])) {
        foreach ($data['steps'] as $step) {
            if (($step['type'] ?? '') === 'model_output' && !empty($step['content'][0]['text'])) {
                $textoJSON = $step['content'][0]['text'];
                break;
            }
        }
    }

    // Fallback caso a estrutura mude
    if (!$textoJSON) {
        $textoJSON = $data['outputs'][0]['text'] ?? null;
    }

    if (!$textoJSON) {
        return null;
    }

    // 2. Limpa eventuais marcadores Markdown de código que a IA possa incluir
    $textoLimpo = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($textoJSON));

    // 3. Converte o JSON dos posts em um Array PHP
    return json_decode($textoLimpo, true);
}


/**
 * Processa o retorno da IA e salva os posts com datas de publicação variadas.
 * 
 * @param int $userId ID do usuário dono da conta
 * @param array $respostaJsonIA Array decodificado do retorno da Gemini
 * @return bool
 */
function save_ai_posts(int $userId, array $respostaJsonIA): bool
{
    if (empty($respostaJsonIA['posts'])) {
        return false;
    }

    $conn = DB::getConexao();
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, created_at) VALUES (?, ?, ?, ?)");

    $diasEspacados = 0;

    foreach ($respostaJsonIA['posts'] as $post) {
        $title = trim($post['title'] ?? '');
        $content = trim($post['content'] ?? '');

        if (empty($title) || empty($content)) {
            continue;
        }

        // Gera um horário natural espalhado ao longo dos dias (ex: 1 post por dia em horário aleatório)
        $createdAt = generate_post_time('now', -$diasEspacados);

        $stmt->bind_param("isss", $userId, $title, $content, $createdAt);
        $stmt->execute();

        // Incrementa o intervalo para que o próximo post fique no dia anterior/posterior
        $diasEspacados++;
    }

    return true;
}
/**
 * Gera um timestamp 'created_at' humanizado para evitar padrão automático.
 * 
 * @param string|DateTime $dataBase Data inicial de referência ('now', 'yesterday', '2026-07-25')
 * @param int $variacaoDias Dias de intervalo retroativo/futuro para espalhar os posts
 * @return string Data formatada 'Y-m-d H:i:s' para o MySQL
 */
function generate_post_time($dataBase = 'now', int $variacaoDias = 0): string
{
    $data = new DateTime($dataBase);

    // 1. Se houver variação de dias, soma ou subtrai dias aleatoriamente
    if ($variacaoDias !== 0) {
        $diasAleatorios = rand(0, abs($variacaoDias));
        if ($variacaoDias > 0) {
            $data->modify("+{$diasAleatorios} days");
        } else {
            $data->modify("-{$diasAleatorios} days");
        }
    }

    // 2. Define um horário de pico humano realista (entre 08h e 21h)
    $horaHumana = rand(8, 21);
    
    // 3. Adiciona minutos e segundos completamente aleatórios para não ficar zerado
    $minutoHumano = rand(0, 59);
    $segundoHumano = rand(0, 59);

    $data->setTime($horaHumana, $minutoHumano, $segundoHumano);

    return $data->format('Y-m-d H:i:s');
}


function generate_ai_prompt(string $nicho, int $quantidade = 1, string $tomDeVoz = 'engajador'): string
{
    $prompt = <<<PROMPT
        Você é um especialista em criação de conteúdo, social media e copywriting.

        NICHO / TEMA: {$nicho}
        QUANTIDADE DE POSTS: {$quantidade}
        TOM DE VOZ: {$tomDeVoz}

        REGRAS OBRIGATÓRIAS:
        - Gere exatamente {$quantidade} publicação(ões) inédita(s).
        - Cada publicação deve conter um "title" (título atrativo, máximo 255 caracteres) e um "content" (conteúdo completo do post).
        - O conteúdo deve ser natural, bem escrito e adaptado ao tom de voz informado.
        - Não utilize formatação Markdown antes ou depois do JSON.
        - Nao use muitos emojs. (use estrategicamente)
        - Não inclua explicações, apenas retorne o JSON válido.

        FORMATO DE SAÍDA:
        {
        "nicho": "{$nicho}",
        "posts": [
            {
            "title": "Título impactante da publicação",
            "content": "Conteúdo detalhado da publicação... (curto, claro e objetivo)"
            }
        ]
        }
        PROMPT;

    return $prompt;
}