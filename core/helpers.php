<?php

// classes

class Router {
    private static array $routes = [];
    private static $notFoundHandler = null;

    /**
     * Adiciona uma rota
     * @param string $method  GET, POST, etc
     * @param string $route   Ex: /categorias/update/{id}  |  /posts/{id?}/{slug?}
     * @param string|callable $callback "Controller@method" ou função
     * @param array  $middlewares nomes de funções ou "Classe@metodo"
     */
    public static function add(string $method, string $route, $callback, array $middlewares = []): void {
        self::$routes[] = [
            'method'      => strtoupper($method),
            'route'       => self::normalize($route),
            'callback'    => $callback,
            'middlewares' => $middlewares
        ];
    }

    public static function setNotFound($callback): void {
        self::$notFoundHandler = $callback;
    }

    public static function run(): void {
        // Captura URL "limpa" enviada pelo .htaccess para ?url=...
        $url = isset($_GET['url']) ? self::normalize($_GET['url']) : '';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Fallback: HEAD deve comportar-se como GET
        if ($method === 'HEAD') $method = 'GET';

        foreach (self::$routes as $r) {
            if ($method !== $r['method']) continue;

            [$regex, $paramNames] = self::compile($r['route']);

            if (preg_match($regex, $url, $matches)) {
                // $matches[0] é a string inteira; parâmetros começam em 1
                array_shift($matches);

                // Monta parâmetros na mesma ordem dos placeholders
                $params = [];
                foreach ($paramNames as $i => $name) {
                    $params[$name] = $matches[$i] ?? null;
                }

                // Middlewares
                foreach ($r['middlewares'] as $mw) {
                    if (is_string($mw)) {
                        $result = null;
                        if (function_exists($mw)) {
                            $result = call_user_func_array($mw, array_values($params));
                        } elseif (strpos($mw, '@') !== false) {
                            [$class, $methodName] = explode('@', $mw, 2);
                            if (class_exists($class) && method_exists($class, $methodName)) {
                                $result = call_user_func_array([$class, $methodName], array_values($params));
                            } else {
                                throw new Exception("Middleware $mw não existe.");
                            }
                        } else {
                            throw new Exception("Middleware $mw não é válido.");
                        }
                        if ($result === false) {
                            return; // interrompe o fluxo da rota
                        }
                    } elseif (is_callable($mw)) {
                        if (call_user_func_array($mw, array_values($params)) === false) {
                            return;
                        }
                    }
                }

                // Callback principal
                if (is_string($r['callback']) && strpos($r['callback'], '@') !== false) {
                    [$class, $methodName] = explode('@', $r['callback'], 2);
                    if (class_exists($class) && method_exists($class, $methodName)) {
                        call_user_func_array([new $class, $methodName], array_values($params));
                    } else {
                        throw new Exception("Controller ou método não encontrado: {$r['callback']}");
                    }
                } else {
                    call_user_func_array($r['callback'], array_values($params));
                }
                return;
            }
        }

        // 404
        http_response_code(404);
        if (self::$notFoundHandler) {
            call_user_func(self::$notFoundHandler);
        } else {
            // View::render('errors.404', ['title' => "Página não encontrada"]);
            die('Pagina nao encotrada');
        }
    }

    /** Normaliza caminhos removendo barras nas pontas e espaços */
    private static function normalize(string $path): string {
        return trim(trim($path), '/');
    }

    /**
     * Compila a rota para regex e devolve [regex, nomesDosParametros]
     *
     * Regras:
     * - {id}   => segmento OBRIGATÓRIO
     * - {id?}  => segmento OPCIONAL, a BARRA que o antecede também fica opcional
     * Limitação: parâmetros opcionais funcionam melhor no fim da rota.
     */
    private static function compile(string $route): array {
        $paramNames = [];
        $regex = '';
        $offset = 0;

        // Encontra todos os tokens {name} ou {name?}
        if (preg_match_all('#\{([a-zA-Z_][a-zA-Z0-9_]*)(\?)?\}#', $route, $m, PREG_OFFSET_CAPTURE)) {
            foreach ($m[0] as $idx => $match) {
                $token      = $match[0];
                $pos        = $match[1];
                $name       = $m[1][$idx][0];
                $isOptional = $m[2][$idx][0] === '?';

                // Parte estática antes do token
                $static = substr($route, $offset, $pos - $offset);
                // Se a parte estática terminar com '/', vamos incluí-la no grupo opcional quando for opcional
                $staticEndsWithSlash = (substr($static, -1) === '/');

                if ($isOptional && $staticEndsWithSlash) {
                    // remove a '/' do fim da parte estática para movê-la para dentro do grupo opcional
                    $static = substr($static, 0, -1);
                    $regex .= preg_quote($static, '#') . '(?:/([^/]+))?';
                } else {
                    $regex .= preg_quote($static, '#');
                    $regex .= $isOptional ? '([^/]+)?' : '([^/]+)';
                }

                $paramNames[] = $name;
                $offset = $pos + strlen($token);
            }
        }

        // Parte estática restante
        $tail = substr($route, $offset);
        $regex .= preg_quote($tail, '#');

        // Âncoras
        $regex = '#^' . $regex . '$#';

        return [$regex, $paramNames];
    }
}


class Auth {
    
    /**
     * Inicia a sessão se ainda não estiver ativa
     */
    private static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Define a sessão do usuário (Login)
     */
    public static function login(User $user) {
        self::init();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_role'] = $user->role;
        session_regenerate_id(true); // Segurança contra fixação de sessão
        return true;
    }

    /**
     * Finaliza a sessão (Logout)
     */
    public static function logout() {
        self::init();
        session_unset();
        session_destroy();
        header("Location: " . url('/login'));
        exit;
    }

    /**
     * Verifica se existe um usuário logado
     */
    public static function check() {
        self::init();
        return isset($_SESSION['user_id']);
    }

    /**
     * Retorna o ID do usuário logado
     */
    public static function id() {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Retorna a instância do Model User do usuário logado
     */
    public static function user() {
        if (self::check()) {
            return User::find(self::id());
        }
        return null;
    }

    /**
     * Protege rotas: Redireciona se não estiver logado
     */
    public static function guard() {
        if (!self::check()) {
            header("Location: " . url('/login'));
            exit;
        }
    }

    /**
     * Verifica se o usuário tem uma permissão específica (Role)
     */
    public static function hasRole($role) {
        self::init();
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role);
    }
}


// functions

function url($path = '')
{
    return $_ENV['APP_URL'] . ltrim($path, '/');
}

function redirect($path = '')
{
    header("Location: " . url($path));
    exit;
}

function assets($path = '')
{
    return url('assets/' . ltrim($path, '/'));
}

function view($page, $data = [])
{
    // transforma array em variáveis
    extract($data);

    include "pages/{$page}.php";
}

function formatPriceToUSD($usd_value) {
    // Taxa de câmbio fixa (ou você pode buscar de uma API no futuro)
    $exchange_rate = 64.00; 
    
    $current_lang = $_SESSION['lang'] ?? 'pt';

    if ($current_lang === 'pt') {
        // Converte para Metical (MZN)
        $mzn_value = $usd_value * $exchange_rate;
        // Formato: 1.500 MT
        return number_format($mzn_value, 0, ',', '.') . ' MT';
    } else {
        // Mantém em Dólar (USD)
        // Formato: $25.00
        return '$' . number_format($usd_value, 2, '.', ',');
    }
}

function loadRoutesRecursively($directory) {
    // Cria um iterador para o diretório informado
    $iterator = new RecursiveDirectoryIterator($directory);
    
    // Configura para percorrer subpastas recursivamente
    $recursiveIterator = new RecursiveIteratorIterator($iterator);

    foreach ($recursiveIterator as $file) {
        // Verifica se é um arquivo (não pasta) e se tem a extensão .php
        if ($file->isFile() && $file->getExtension() === 'php') {
            require_once $file->getPathname();
        }
    }
}

/**
 * Padroniza a resposta JSON para as APIs internas
 * 
 * @param bool $success Indica se a operação foi bem sucedida
 * @param string $message Mensagem de feedback para o usuário
 * @param array $data Dados adicionais (opcional)
 * @param int $code Código de status HTTP (padrão 200)
 */
function jsonResponse($success, $message, $data = [], $code = 200) {
    // Define o cabeçalho como JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // Define o código de resposta HTTP
    http_response_code($code);

    // Estrutura o corpo da resposta
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

    // Interrompe a execução para evitar que outros buffers sejam enviados
    exit;
}

/**
 * Verifica se um usuário está banido
 */
function isUserBanned($user_id) {
    return 0;
}