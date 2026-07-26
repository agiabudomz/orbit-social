<?php

Router::add('GET', 'newpost', function() {
    view('posts/newpost');
}, ['checkAuth']);

Router::add('GET', 'newpost-ai', function() {
    view('posts/newpost-ai');
}, ['checkAuth']);
