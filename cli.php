<?php

require_once 'vendor/autoload.php';

use MLEncontraLinkNãoPatrocinado\EncontraLink;

$encontra_link = new EncontraLink();
try {
    $pagina = $encontra_link->encontra($argv[1]);
    echo "Página ($encontra_link->página): $pagina";
} catch (\Exception $e) {
    echo $e->getMessage();
}

echo "\n";
