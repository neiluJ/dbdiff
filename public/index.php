<?php
require_once __DIR__ .'/../vendor/autoload.php';

use Fwk\Core\Application, 
    Fwk\Core\Descriptor;

$desc = new Descriptor(__DIR__ .'/../DbDiff/fwk.xml');
$app = Application::autorun($desc);