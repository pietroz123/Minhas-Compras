<?php
    include $_SERVER['DOCUMENT_ROOT'].'/cabecalho.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';
?>

<?php
    verifica_usuario();

    $usuario = join_usuario_comprador($conexao, $_SESSION['login']);
?>

    <pre><?php print_r($usuario); ?></pre>

    <pre><?php print_r($_SESSION); ?></pre>

    <h2 class="titulo-perfil">Perfil de Pietro</h2>

    <div class="grid dados-perfil">
        <div class="row">
            <div class="col col-md-5 mb-3">
                <div class="card z-depth-2">
                    <div class="card-header default-color white-text">Dados Pessoais</div>
                    <div class="card-body">
                        <h5 class="nome-perfil"><?= $usuario['Primeiro_Nome']; ?></h5>
                        <p class="texto-dados">(15) 99713-6093</p>
                        <p class="texto-dados">410.242.338-98</p>
                        <p class="texto-dados">pietroz@terra.com.br</p>
                    </div>
                </div>
            </div>
            <div class="col col-md-7 mb-3">
                <div class="card z-depth-2">
                    <div class="card-header default-color white-text">Endereço</div>
                    <div class="card-body">
                        <h5 class="nome-perfil">Pietro</h5>
                        <p class="texto-dados">Rua Cipriano Fernandes, 79</p>
                        <p class="texto-dados">Sorocaba - SP</p>
                        <p class="texto-dados">18017-250</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card z-depth-2">
                    <div class="card-header default-color-dark white-text">
                        Grupos
                        <button class="btn default-color botao-pequeno ml-2 btn-recarregar" style="float: right;"><i class="fas fa-sync-alt" id="icone-recarregar"></i> recarregar grupos</button>
                        <button class="btn default-color botao-pequeno" style="float: right;" data-toggle="modal" data-target="#modal-criar-grupo">criar grupo</button>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="row">
                                        <th class="col-sm-4">Nome</th>
                                        <th class="col-sm-3">Data Criação</th>
                                        <th class="col-sm-3">Número Membros</th>
                                        <th class="col-sm-2">Visualizar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="row">
                                        <td class="col-sm-4">Família</td>
                                        <td class="col-sm-3">2019-20-01</td>
                                        <td class="col-sm-3">2</td>
                                        <td class="col-sm-2">
                                            <button class="btn btn-info botao-pequeno btn-membros" id="1">Membros</button>
                                        </td>
                                    </tr>
                                    <tr class="row">
                                        <td class="col-sm-4">Amigos</td>
                                        <td class="col-sm-3">2019-24-01</td>
                                        <td class="col-sm-3">4</td>
                                        <td class="col-sm-2">
                                            <button class="btn btn-info botao-pequeno">Membros</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para membros do Grupo -->
    <div class="modal" id="modal-membros-grupo">
        <div class="modal-dialog" id="membros-grupo">
            <!-- Preenchido com AJAX (JS) -->
        </div>
    </div>

    <?php
        include 'modal-criar-grupo.php';
    ?>

<?php
    include $_SERVER['DOCUMENT_ROOT'].'/rodape.php';
?>

<script>
    // Preenche o modal-membros-grupo utilizando AJAX
    $(".btn-membros").click(function() {
        var id_grupo = $(this).attr("id");        

        $.ajax({
            url: "modal-membros-grupo.php",
            method: "post",
            data: {
                id_grupo: id_grupo
            },
            success: function(data) {
                $("#membros-grupo").html(data);
                $("#modal-membros-grupo").modal("show");
            }
        });
    });

    $(".btn-recarregar").click(function() {
        var icone = document.querySelector("#icone-recarregar");
        
        icone.classList.add('fa-spin');

        setTimeout(function() {
            icone.classList.remove('fa-spin');
        }, 1000);
    });

</script>