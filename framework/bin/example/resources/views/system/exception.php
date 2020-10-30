<?php

$this->layout('layouts/master')
    ->setSection('body', function() use ($exception){
        ?>

        <h3>Exception Occurred</h3>
        <div><?php echo $exception->getMessage(); ?></div>
        <div><?php echo $exception->getTraceAsString(); ?></div>

        <?php
    })->render();
