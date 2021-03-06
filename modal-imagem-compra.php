<?php
    include $_SERVER['DOCUMENT_ROOT'].'/database/dbconnection.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes.php';
?>

<?php
    if (isset($_POST['id_compra'])) {
        $id = $_POST['id_compra'];
        $compra = buscar_compra($dbconn, $id);
?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
    </div>
    <div class="modal-body">
        <div class="grid">
            <div class="row">
                <div class="col">
                <?php
                    if (empty($compra['Imagem'])) {
                ?>
                        <div class="alert alert-danger" role="alert"><p>Imagem inexistente!</p></div>
                <?php
                    }
                    else {
                ?>
                        <img src="scripts/imagem.php?imagem=<?= $compra['Imagem'] ?>" class="responsive">
                <?php
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        
    </div>
</div>

<?php
    }
?>