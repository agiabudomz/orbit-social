
<?php

function __($key) {
    static $translations = null;

    if ($translations === null) {
        $translations = [
            'pt' => [
                'home'       => 'Início',
                'welcome'    => 'Bem-vindo',
                'create'     => 'Criar',
                'edit'       => 'Editar',
                'delete'     => 'Excluir',
                'save'       => 'Salvar',
                'cancel'     => 'Cancelar',
                'back'       => 'Voltar',
                'search'     => 'Pesquisar',
                'login'      => 'Entrar',
                'logout'     => 'Sair',
                'register'   => 'Cadastrar',
                'profile'    => 'Perfil',
                'settings'   => 'Configurações',
                'contact'    => 'Contacto',
                'all_rights' => 'Todos os direitos reservados.'
            ],
            'en' => [
                'home'       => 'Home',
                'welcome'    => 'Welcome',
                'create'     => 'Create',
                'edit'       => 'Edit',
                'delete'     => 'Delete',
                'save'       => 'Save',
                'cancel'     => 'Cancel',
                'back'       => 'Back',
                'search'     => 'Search',
                'login'      => 'Login',
                'logout'     => 'Logout',
                'register'   => 'Register',
                'profile'    => 'Profile',
                'settings'   => 'Settings',
                'contact'    => 'Contact',
                'all_rights' => 'All rights reserved.'
            ]
        ];
    }

    $lang = $_SESSION['lang'] ?? 'pt';

    // Retorna a tradução do idioma atual -> fallback para PT -> fallback para a própria chave
    return $translations[$lang][$key] ?? $translations['pt'][$key] ?? $key;
}