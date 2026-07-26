<?php

// register
Router::add('POST', '/api/register', function() {
    $data = $_POST;

    // 1. Verificação de campos obrigatórios
    $required = ['name', 'username', 'email', 'password', 'password_confirmation'];
    foreach ($required as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            jsonResponse(false, "Todos os campos obrigatórios devem ser preenchidos.");
            exit;
        }
    }

    // 2. Sanitização e Normalização dos Dados
    $name     = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
    $username = strtolower(trim($data['username']));
    $email    = strtolower(trim($data['email']));
    $title    = !empty($data['title']) ? htmlspecialchars(trim($data['title']), ENT_QUOTES, 'UTF-8') : null;
    $password = $data['password'];

    // 3. Validações de Formato
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, "O e-mail informado não possui um formato válido.");
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        jsonResponse(false, "O nome de usuário deve conter de 3 a 30 caracteres (letras, números e underline).");
        exit;
    }

    if (strlen($password) < 6) {
        jsonResponse(false, "A senha deve ter no mínimo 6 caracteres.");
        exit;
    }

    if ($password !== $data['password_confirmation']) {
        jsonResponse(false, "As senhas não coincidem.");
        exit;
    }

    // 4. Verificação de Unicidade no Banco de Dados (Evita duplicados)
    $conn = DB::getConexao();

    // Checa Username
    $stmtUser = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmtUser->bind_param("s", $username);
    $stmtUser->execute();
    if ($stmtUser->get_result()->num_rows > 0) {
        jsonResponse(false, "Este nome de usuário (@{$username}) já está em uso.");
        exit;
    }

    // Checa Email
    $stmtEmail = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmtEmail->bind_param("s", $email);
    $stmtEmail->execute();
    if ($stmtEmail->get_result()->num_rows > 0) {
        jsonResponse(false, "Este endereço de e-mail já está cadastrado.");
        exit;
    }

    // 5. Criação do Usuário
    $user = new User([
        'name'      => $name,
        'username'  => $username,
        'email'     => $email,
        'title'     => $title,
        'password'  => password_hash($password, PASSWORD_DEFAULT),
        'user_type' => 'user'
    ]);

    $userID = $user->save();

    if ($userID) {
        $user->id = $userID;
        Auth::login($user);

        jsonResponse(true, "Conta criada com sucesso!", [
            'redirect' => url('/feed')
        ]);
    } else {
        jsonResponse(false, "Erro interno ao criar sua conta. Tente novamente.");
    }
});

// login
Router::add('POST', '/api/login', function() {
    $data = $_POST;

    // Aceita 'login' (ou 'username'/'email') e 'password'
    $loginInput = trim($data['login'] ?? $data['username'] ?? $data['email'] ?? '');
    $password   = $data['password'] ?? '';

    // 1. Verificação de campos obrigatórios
    if (empty($loginInput) || empty($password)) {
        jsonResponse(false, "Por favor, informe o seu usuário/e-mail e a senha.");
        exit;
    }

    // 2. Normalização do login (Username/E-mail sempre em minúsculo)
    $loginNormalized = strtolower($loginInput);

    // 3. Busca do Usuário no Banco de Dados (Por username OU email)
    $conn = DB::getConexao();

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param("ss", $loginNormalized, $loginNormalized);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se o usuário não for encontrado
    if ($result->num_rows === 0) {
        jsonResponse(false, "Usuário/E-mail ou senha incorretos.");
        exit;
    }

    $userData = $result->fetch_assoc();

    // 4. Verificação da Senha
    if (!password_verify($password, $userData['password'])) {
        jsonResponse(false, "Usuário/E-mail ou senha incorretos.");
        exit;
    }

    // 5. Instanciação do Objeto User e Autenticação na Sessão
    $user = new User($userData);
    Auth::login($user);

    // 6. Resposta de Sucesso
    jsonResponse(true, "Login realizado com sucesso!", [
        'redirect' => url('/feed')
    ]);
});
