<?php

/*
 * @var \Framework\Template\PhpRenderer $this
 */
$this->extend('layout/default');
$this->params['title'] = 'Hello'; //$this - наш renderer
?>

<div class="jumbotron">
    <h1>Hello!</h1>
    <p>
        Congratulations! You have successfully created your application.
    </p>
</div>
