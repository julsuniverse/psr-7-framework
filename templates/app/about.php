<?php

/*
 * @var \Framework\Template\PhpRenderer $this
 */
$this->extend('layout/default');
$this->params['title'] = 'About';

?>
<?php $this->beginBlock('breadcrumbs');?>
<ul class="breadcrumb">
    <li><a href="/">Home</a></li>
    <li class="active">Cabinet</li>
</ul>
<?php $this->endBlock() ?>

<h1>About the site</h1>

