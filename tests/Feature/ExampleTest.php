<?php

test('the application redirects the root to the default locale', function () {
    $this->get('/')->assertRedirect('/en');
});
