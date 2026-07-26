<?php

Router::add('GET', '/home', function() {
    view('feed');
});


Router::add('GET', '/', function() {
    view('feed');
});

Router::add('GET', '/feed', function() {
    view('feed');
});
