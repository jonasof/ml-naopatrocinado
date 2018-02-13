<?php

require_once 'vendor/autoload.php';

use ML_Encontra_Link\EncontraLink;

$event_handler = new ML_Encontra_Link\Event;

$encontra_link = new EncontraLink($event_handler);
try {
    $pagina = $encontra_link->encontra($argv[1]);
    echo "Página ($encontra_link->página): $pagina";
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";
