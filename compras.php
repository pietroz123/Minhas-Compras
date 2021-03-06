<?php 
    include $_SERVER['DOCUMENT_ROOT'].'/cabecalho.php'; 
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-grupos.php';
    include $_SERVER['DOCUMENT_ROOT'].'/persistence/CompraDAO.php';
?>

<?php
    verifica_usuario();
    mostra_alertas();
    
    // Recupera todos os grupos do usuário
    $grupos = recuperar_grupos($dbconn, $_SESSION['login-username']);
    
?>

<h1 style="text-align: left;" class="titulo-site">Grupos</h1>
<div class="alert alert-info">
    <div>Selecione as compras que deseja visualizar:</div>
</div>


<div class="cartao-novo cartao-usuario">
    <a role="button" class="link-cartao-minhas-compras" id-comprador="<?= $_SESSION['login-id-comprador']; ?>">
        <div class="container">
            <div class="row">
                <div class="col-4 col-sm-2 col-md-4 col-lg-3 col-xl-2 cg-coluna-imagem-usuario pink darken-4">
                    <img src="img/usuario.png" alt="Imagem perfil" class="rounded-circle white cg-imagem">
                </div>
                <div class="col col-sm-10 col-md col-lg col-xl cg-coluna-informacoes-usuario">
                    <div class="row cg-linha-nome">
                        <div class="col-sm">
                            <div class="cg-nome-usuario font-weight-bold">Minhas Compras</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm cg-login-nome text-uppercase"><?= $_SESSION['login-nome']; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>


<hr>

<!-- Cartões dos Grupos -->
<div class="cartoes">
<?php
    $i = 0;
    foreach ($grupos as $grupo) {
        $i++;
?>
    <div class="cartao-novo">
        <a role="button" class="link-cartao-grupo" id-grupo="<?= $grupo['ID']; ?>">
            <div class="container">
                <div class="row">
                    <div class="col-sm-2 col-md-4 col-lg-3 col-xl-2 cg-coluna-imagem indigo">
                        <img src="img/grupo.png" alt="Imagem perfil" class="rounded-circle white cg-imagem">
                    </div>
                    <div class="col-sm-10 col-md col-lg col-xl cg-coluna-informacoes">
                        <div class="row cg-linha-nome">
                            <div class="col-sm-12">
                                <div class="cg-nome"><?= $grupo['Nome']; ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-10 col-lg-8 small-bold">Data de criação</div>
                            <div class="col-sm-6 col-md col-lg small-faded"><?= date("d/m/Y", strtotime($grupo['Data_Criacao'])); ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-10 col-lg-8 small-bold">Número de membros</div>
                            <div class="col-sm-6 col-md col-lg small-faded"><?= $grupo['Numero_Membros']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php
    }
?>
</div>
<!-- Fim Cartões dos Grupos -->

<!-- Cartões de Informações -->
<div class="cartoes informacoes d-flex flex-column">
<?php    
    if ($i == 0) {
?>
        <div class="alert alert-info text-left cartao-info">
            <div class="alert-heading">
                <strong>Você não está em nenhum grupo, logo apenas poderá ver suas compras.</strong>
            </div>
            <a href="perfil-usuario.php#cartao-grupos-usuario" class="alert-link">Deseja criar um grupo?</a>
        </div>
<?php
    }
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
?>
</div>
<!-- Fim Cartões de Informações -->

<hr>


<div id="container-compras" style="display: none;">

    <h1 class="titulo-site mt-5" id="titulo-compras">Lista de Compras</h1>

    <!-- Para ordenação da data -->
    <label>
        Data
        <select name="ordenacao-data" id="ordenacao-data" class="custom-select custom-select-sm form-control form-control-sm">
            <option>mais recente</option>
            <option>mais antiga</option>
        </select>
    </label>

    <!-- Tabela com as compras -->
    <table class="table table-hover" id="tabela-compras">

        <thead class="thead-dark">
            <tr>
                <th class="th-sm t-observacoes">Observacoes</th>
                <th class="th-sm t-data">Data</th>
                <th class="th-sm t-id">ID</th>
                <th class="th-sm t-valor">Valor</th>
                <th class="th-sm t-desconto">Desconto</th>
                <th class="th-sm t-pagamento">Pagamento</th>
                <th class="th-sm t-nome">Comprador</th>
                <th class="th-sm t-editar">Editar</th>
            </tr>
        </thead>

        <tbody id="compras-datatable">
            <!-- Preenchido ao clicar nas compras desejadas -->
        </tbody>

    </table>

</div>


<!-- Modal para detalhes da Compra -->
<div class="modal" id="modal-detalhes-compra">
    <div class="modal-dialog modal-lg" id="detalhes-compra">
        <!-- Preenchido com AJAX (JS) -->
    </div>
</div>

<!-- Modal para imagem da Compra -->
<div class="modal" id="modal-imagem-compra">
    <div class="modal-dialog" id="imagem-compra">
        <!-- Preenchido com AJAX (JS) -->        
    </div>
</div>


<?php include $_SERVER['DOCUMENT_ROOT'].'/rodape.php'; ?>
<script src="js/compras.js"></script>