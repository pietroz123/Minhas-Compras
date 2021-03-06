<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/cabecalho.php';
    include $_SERVER['DOCUMENT_ROOT'].'/persistence/CompraDAO.php';

    verifica_usuario();
?>

    <!-- Header com o título e o select com os anos -->
    <div class="header-relatorios">
    
        <h1 class="titulo-site">Relatórios</h1>

        <!-- Anos disponíveis para visualização -->
        <div class="anos-disponiveis mb-4"></div>

    </div>

    <!-- Mensagem de ajuda -->
    <?php
        if (CompraDAO::numeroCompras($dbconn, $_SESSION['login-id-comprador']) === 0) {
    ?>
        <div class="alert alert-info text-left cartao-info">
            <div class="alert-heading">
                <strong>Você não adicionou nenhuma compra ainda.</strong>
            </div>
            <a href="formulario-compra.php" class="alert-link">Deseja adicionar uma compra?</a>
        </div>
    <?php
        }
        else {
    ?>
        <div class="mensagem-ajuda">
            <div class="alert alert-primary">
                <span>Selecione o ano que deseja visualizar.</span>
            </div>
        </div>
    <?php
        }
    ?>

    <!-- Gráfico de compras -->
    <div class="grafico-compras-container">
        <div id="grafico-compras"></div>
        <div class="overlayLoading" style="display: none;">
            <div class="overlayLoading__inner">
                <div class="overlayLoading__content">
                    <span class="spinner"></span>
                    <span class="text">Carregando...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Div onde as compras de determinado dia irão aparecer -->
    <div id="compras"></div>


<?php include $_SERVER['DOCUMENT_ROOT'].'/rodape.php'; ?>
<script src="js/relatorios.js"></script>