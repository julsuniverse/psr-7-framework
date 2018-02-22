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
        <div class="panel panel-default">
            <div class="panel-heading">Cabinet</div>
            <div class="panel-body">
                Cabinet navigation
            </div>
        </div>
    </div>


</div>