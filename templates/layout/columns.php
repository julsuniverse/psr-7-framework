<?php
/**
 * @var \Framework\Template\PhpRenderer $this
 */
?>
<?php $this->extend('layout/default'); ?>


<?php $this->beginBlock('content');?>
    <div class="row">
        <div class="col-md-9 .col-lg-push-9 col-sm-12">
            <?= $this->renderBlock('main'); ?>
        </div>
        <div class="col-md-3 .col-lg-pull-3 col-sm-12">
            <?= $this->renderBlock('sidebar'); ?>
        </div>
    </div>
<?php $this->endBlock() ?>