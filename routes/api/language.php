<?php

Router::add('GET', 'api/language-set/{lang}', function($lang) {
    $_SESSION['lang'] = $lang;
    $_SESSION['lang_set'] = true; // Marca que o usuário já escolheu
    echo json_encode(['success' => true]);
});



Router::add('GET', 'lang/{lang}', function($lang) {
    $_SESSION['lang'] = $lang;
    $_SESSION['lang_set'] = true; // Marca que o usuário já escolheu
    redirect('/');
});