<?php

namespace MLEncontraLinkNãoPatrocinado;

use Sabre\Event\EventEmitter;
use Goutte\Client as GoutteClient;

class EncontraLink
{
    public $preços = [];
    public $página = 0;

    protected $event_emitter;
    protected $client;
    /**
     * @var \Symfony\Component\DomCrawler\Crawler $crawler
     */
    protected $crawler;

    public function __construct(EventEmitter $event_emitter = null)
    {
        $this->event_emitter = $event_emitter ?? new EventEmitter();
        $this->client = $this->configurarClient();
        $this->crawler = null;
    }

    protected function configurarClient() : GoutteClient
    {
        $client = new GoutteClient();

        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $client->setHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);
        }

        return $client;
    }

    public function encontra($página)
    {
        $this->encontraPrimeiraPáginaOrdenadaPorPreço($página);

        processar_página:

        $this->registrarAvanço();
        $this->obtémPreços();

        if ($this->preçosAindaEstãoOrdenados()) {
            if ($this->éAÚltimaPágina()) {
                throw new Exceções\LinkNãoEncontrado();
            }

            $this->irParaPróximaPágina();

            goto processar_página;
        }

        return $this->crawler->getUri();
    }

    protected function encontraPrimeiraPáginaOrdenadaPorPreço($página)
    {
        $this->crawler = $this->client->request('GET', $página);

        $this->clicarEmOrdenaçãoDeMenorPreço();
        $this->irParaPrimeiraPágina();
    }

    protected function clicarEmOrdenaçãoDeMenorPreço()
    {
        try {
            $this->crawler = $this->client->click($this->crawler->selectLink('Menor preço')->link());
        } catch (\Exception $e) {
        }
    }

    protected function irParaPrimeiraPágina()
    {
        if (preg_match('/_Desde_\d+/', $this->crawler->getUri())) {
            $url = preg_replace('/_Desde_\d+/', '', $this->crawler->getUri());
            $this->crawler = $this->client->request('GET', $url);
        }
    }

    protected function obtémPreços()
    {
        if ($this->crawler->filter('.ui-search-item__group--price .price-tag-fraction')->count() === 0) {
             throw new Exceções\PreçosNãoEncontrados();
        }

        $this->crawler->filter('.ui-search-item__group--price .price-tag-fraction')->each(function ($node) {
            $this->preços[] = (float) str_replace([',', '.'], "", $node->text());
        });
    }

    protected function éAÚltimaPágina() : bool
    {
        return $this->crawler->filter('.andes-pagination__button--next.andes-pagination__button--disabled')->count()
            > 0;
    }

    protected function preçosAindaEstãoOrdenados() : bool
    {
        for ($x = 0; $x < count($this->preços)-1; $x++) {
            $preçoAtual = $this->preços[$x];
            $preçoPosterior = $this->preços[$x+1];

            $tolerância = 1.5; // 50% é um adicional para evitar uma falsa detecção pois
                               // algumas vezes o mercado livre não ordena corretamente os produtos

            if ($preçoAtual > $preçoPosterior * $tolerância) {
                return false;
            }
        }

        return true;
    }

    protected function irParaPróximaPágina()
    {
        $link = $this->crawler->filter('.andes-pagination__button--next .andes-pagination__link')->link();
        $this->crawler = $this->client->click($link);
    }

    protected function registrarAvanço()
    {
        $this->página++;
        $this->event_emitter->emit('próxima_pagina', [$this->página]);
    }
}
