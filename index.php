<?php
    require_once 'vendor/autoload.php';

    use MLEncontraLinkNãoPatrocinado\{EncontraLink};
    use MLEncontraLinkNãoPatrocinado\Exceções\{LinkNãoEncontrado, PreçosNãoEncontrados};
    use Sabre\Event\EventEmitter;

    header( 'Content-type: text/html; charset=utf-8' );
    header('X-Accel-Buffering: no');

    if (!isset($_POST['pagina'])) {
        ?>
        <div>
            <h1 style="text-align: center;">Procurar anúncios não patrocinados no Mercado Livre</h1>

            <form action="." method="POST" style="text-align: center;">
                <p>
                    Insira o link de uma pesquisa:
                </p>
                <p>
                    <input type="text" name='pagina' size=90 style="max-width: 100%"></input>
                </p>
                <p>
                    <input type="submit" value='Procurar'>
                </p>
            </form>

            <hr />

            <h3>Explicação</h3>

            <p>
              O mercado livre coloca anúncios não premium (gratuitos) com menor prioridade.
              Se ordenamos uma pesquisa pelo menor preço, encontramos esses anúncios
              não patrocinados apenas ao passar por todos os anúncios premium,
              sendo assim difícil identificar todas as possíveis ofertas.
            </p>

            <p>
              Esta ferramenta foi desenvolvida para navegar por todas as páginas ordenadas por custo
              crescente até encontrar a primeira que possui tais anúncios não patrocinados.
            </p>

            <p style="color: darkred;">
                Limitações: O mercado livre só aceita até 40 páginas de resultados.
                Buscas muito genéricas provavelmente demorarão mais e não encontrarão a
                primeira página não patrocinada.
            </p>

            <p>Exemplo:</p>
            <p><img src='./exemplo.jpg' width='500' /></p>

            <p><a href='http://github.com/jonasof/ml-nao-patrocinado'>Código Fonte</a></p>
        </div>

        <?php
    } else {
        $event_emitter = new EventEmitter();
        $encontra_link = new EncontraLink($event_emitter);

        echo '<h1 style="text-align: center;">Procurar anúncios não patrocinados no Mercado Livre</h1>';

        echo "<p><a href='.'>Voltar</a></p>";

        echo "<h2>Procurando link...</h2> ";

        if (!validarLink($_POST['pagina'])) {
            exit();
        }

        try {
            $event_emitter->on('próxima_pagina', function ($página) {
                echo "$página, ";
                ob_flush();
                flush();
            });
            $pagina = $encontra_link->encontra($_POST['pagina']);

            echo "<h2 style='color: darkgreen;'>Página encontrada: $encontra_link->página</h2>";
            echo "<p><a href='$pagina' noreferrer noopener>$pagina</a></p>";
        } catch (LinkNãoEncontrado | PreçosNãoEncontrados $e) {
            echo $e->getMessage();
        }
    }

function validarLink($link) : bool {
    if (!filter_var($_POST['pagina'], FILTER_VALIDATE_URL)) {
        echo "O texto inserido não é um link";
        return false;
    }

    $parsed = parse_url($link);

    if (strstr($parsed['host'], 'mercadolivre') === false) {
        echo "O link inserido não parece ser do mercado livre";
        return false;
    }

    return true;
}