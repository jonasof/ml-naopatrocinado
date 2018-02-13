<?php

namespace ML_Encontra_Link;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class EncontraLink
{
    public $preços = [];
    public $página = 1;

    public function __construct(Event $event_emitter)
    {
        $this->event_emitter = $event_emitter;
    }

    public function encontra($página)
    {
        $client = new Client();

        $this->event_emitter->emit('proxima_pagina', [$this->página]);
        $crawler = $client->request('GET', $this->encontraPáginaOrdenada($página));

        obter:
        $this->obtémPreços($crawler);

        if (!$this->estáOrdenado()) {
            return $crawler->getUri();
        } else {
            if (!$crawler->filter('.pagination__next.pagination--disabled')->count()) {
                $crawler = $client->click($crawler->filter('.pagination__next a')->link());
                $this->página++;
                $this->event_emitter->emit('proxima_pagina', [$this->página]);
                goto obter;
            } else {
                throw new \Exception("Não conseguimos encontrar a página");
            }
        }
    }

    public function encontraPáginaOrdenada ($página)
    {
        return $página;
    }

    public function obtémPreços(Crawler $crawler)
    {
        $crawler->filter('.price__fraction')->each(function ($node) {
            $this->preços[] = (float) str_replace([',', '.'], "", $node->text());
        });
    }

    public function estáOrdenado()
    {
        for ($x = 0; $x < count($this->preços)-1; $x++) {
            if ($this->preços[$x] > $this->preços[$x+1]) {
                return false;
            }
        }
        return true;
    }
}
