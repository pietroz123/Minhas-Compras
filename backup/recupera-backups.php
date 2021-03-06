<section class="container text-left">

<?php

    date_default_timezone_set('America/Sao_Paulo');

    function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    $i = 0;
    foreach (glob("../../private/backups/banco/*.sql") as $filename) {
        $nome = basename($filename);
        $data_criacao = date("d/m/Y H:i:s", filemtime($filename));
        $tamanho = human_filesize(filesize($filename));
?>

        <div class="row">
            <div class="col mb-3">
                <article class="arquivo">
                    <div><?= $nome ?></div>
                    <div class="font-small font-weight-bold"><?= $data_criacao ?></div>
                    <div class="font-small font-weight-bold"><?= $tamanho ?></div>
                </article>
            </div>
            <div class="col-12 col-md-3 d-flex justify-content-center flex-row flex-md-column">
                <button class="btn btn-info botao botao-pequeno" style="max-width: 10em;" nome-arquivo="<?= $nome ?>" id="btn-visualizar-backup">visualizar</button>
                <button class="btn btn-danger botao botao-pequeno" style="max-width: 10em;" nome-arquivo="<?= $nome ?>" id="btn-remover-backup" data-toggle="confirmation" data-title="Tem certeza?">remover</button>
            </div>
        </div>
        <hr>

<?php
        $i++;
    }
    if ($i == 0) {
?>
        <div class="alert alert-info">Nenhum backup disponível no momento.</div>
<?php
    }
?>

</section>