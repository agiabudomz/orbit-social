<?php

Router::add('GET', 'login', function() {
    view('auth/login');
}, ['checkLoggedIn']);

Router::add('GET', 'register', function() {
    view('auth/register');
}, ['checkLoggedIn']);


Router::add('GET', 'logout', function() {
    session_start();
    session_destroy();
    redirect('login');
});


Router::add('GET', 'setup', function() {
    view('auth/setup');
}, ['checkAuth']); // Apenas logados podem configurar