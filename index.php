<?php
    require_once 'vendor/autoload.php';

    use ML_Encontra_Link\EncontraLink;
    use ML_Encontra_Link\Event;

    header( 'Content-type: text/html; charset=utf-8' );
    header('X-Accel-Buffering: no');

    if (! isset($_POST['pagina'])) {
?>
<h1>Mercado Livre - Encontra página não premium</h1>

<form action="." method="POST">
    Insira o link da primeira página da busca em ordem crescrente de preço<br>
    <input type="text" name='pagina' size=90></input>
    <input type="submit" value='Encontrar'>
</form>

<?php
    } else {
        $event_handler = new Event();
        $encontra_link = new EncontraLink($event_handler);
        try {
            echo "<h2>Procurando página...</h2> ";
            echo "Progresso: ";
            $event_handler->listen('proxima_pagina', function ($página) {
                echo "$página, ";
                ob_flush();
                flush();
            });
            $pagina = $encontra_link->encontra($_POST['pagina']);

            echo "<h2>Achamos a página: $encontra_link->página</h2>";
            echo "<p><a href='$pagina' noreferrer noopener>$pagina</a></p>";
            echo "<p><a href='.'>Voltar</a></p>";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
?>
