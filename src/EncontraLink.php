<?php

namespace ML_Encontra_Link;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class EncontraLink
{
    public $preços = [];
    public $página = 1;

    public function encontra($página)
    {
        $client = new Client();

        $crawler = $client->request('GET', $página);

        obter:
        $this->obtémPreços($crawler);

        if (!$this->estáOrdenado()) {
            return $crawler->getUri();
        } else {
            if (!$crawler->filter('.pagination__next.pagination--disabled')->count()) {
                $crawler = $client->click($crawler->filter('.pagination__next a')->link());
                $this->página++;
                goto obter;
            } else {
                throw new \Exception("Não conseguimos encontrar a página");
            }
        }
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
