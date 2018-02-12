<?php
    require_once 'vendor/autoload.php';

    use ML_Encontra_Link\EncontraLink;

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
        $encontra_link = new EncontraLink();
        try {
            $pagina = $encontra_link->encontra($_POST['pagina']);
            echo "<h2>Achamos a página: $encontra_link->página</h2>";
            echo "<a href='$pagina' noreferrer noopener>$pagina</a><br>";
            echo "<a href='.'>Voltar</a>";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
?>
