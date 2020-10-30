<?php

$this->layout('layouts/master')
    ->setSection('body', function(){
        ?>
        <div>Hello World!</div>
        <?php
    })->render();