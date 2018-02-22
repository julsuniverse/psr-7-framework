<?php
/**
 * @var \Framework\Template\PhpRenderer $this
 */
?>
<?php $this->extend('layout/default'); ?>

<div class="row">
    <div class="col-md-9 .col-lg-push-9 col-sm-12">
        <?= $content ?>
    </div>
    <div class="col-md-3 .col-lg-pull-3 col-sm-12">
        <?= $this->renderBlock('sidebar'); ?>
    </div>


</div>