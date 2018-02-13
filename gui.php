<?php

require 'vendor/autoload.php';

use Gui\Application;
use Gui\Components\Button;
use Gui\Components\InputText;
use Gui\Components\Label;

use ML_Encontra_Link\EncontraLink;
use ML_Encontra_Link\Event;

$application = new Application();

$application->on('start', function() use ($application) {
    $field = (new InputText())
       ->setLeft(10)
       ->setTop(50)
       ->setWidth(300)
       ->setTitle('Input');

    $button = (new Button())
        ->setLeft(40)
        ->setTop(100)
        ->setWidth(200)
        ->setValue('Buscar no Mercado Livre');

    $result = (new Label())
        ->setLeft(40)
        ->setTop(200)
        ->setWidth(200);

    $button->on('click', function() use ($button, $field, $result) {
        $event_handler = new Event();
        $encontra_link = new EncontraLink($event_handler);
        try {
            $event_handler->listen('proxima_pagina', function ($página) use ($result) {
                $result->setText("Procurando na página $página");
            });
            $pagina = $encontra_link->encontra($field->getValue());

            $result->setText("<h2>Achamos a página: $encontra_link->página (<a href='$pagina' noreferrer noopener>$pagina</a>)</h2>");
        } catch (\Exception $e) {
            $result->setText($e->getMessage());
        }

    });
});

$application->run();
