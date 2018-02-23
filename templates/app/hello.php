<?php

/*
 * @var \Framework\Template\PhpRenderer $this
 */
$this->extend('layout/default');
$this->params['title'] = 'Hello'; //$this - наш renderer
?>
<?php $this->beginBlock('title');?>Hello<?php $this->endBlock() ?>
<?php $this->beginBlock('meta');?>
    <meta name="description" content="Hello Page description" />
<?php $this->endBlock() ?>

<div class="jumbotron">
    <h1>Hello!</h1>
    <p>
        Congratulations! You have successfully created your application.
    </p>
</div>
