<?php

namespace MLEncontraLinkNãoPatrocinado\Exceções;

class PreçosNãoEncontrados extends \Exception
{
    protected $message = "Não foram encontrados preços nessa página.";
}