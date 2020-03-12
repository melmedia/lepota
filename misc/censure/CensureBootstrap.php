<?php

// phpcs:disable PSR1.Files.SideEffects

namespace lepota\misc\censure;

if (!class_exists('Text_Censure')) {
    require 'ReflectionTypeHint.php';
    require 'UTF8.php';
    require 'Censure.php';
}

class CensureBootstrap extends \Text_Censure
{
}
