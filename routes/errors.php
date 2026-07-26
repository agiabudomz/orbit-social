<?php

Router::add('GET', 'not-found', function() {
    // view('404');
});

Router::add('GET', 'not-authorized', function() {
    // view('403');
});
