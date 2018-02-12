<?php

use ML_Encontra_Link\EncontraLink;

use PHPUnit\Framework\TestCase;

class EncontraLinkTest extends TestCase
{
    public function test () {
        $encontra_link = new EncontraLink();
        $pagina = $encontra_link->encontra('https://informatica.mercadolivre.com.br/memorias-ram/para-pc/ddr4/mem%C3%B3ria-ram-ddr_OrderId_PRICE');
    }
}
