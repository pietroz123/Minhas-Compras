<?php
    include $_SERVER['DOCUMENT_ROOT'].'/cabecalho.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-grupos.php';
?>

<?php
    verifica_usuario();
    mostra_alerta("success");
    mostra_alerta("danger");

    $usuario = join_usuario_comprador($conexao, $_SESSION['login-email']);
?>


    <h2 class="titulo-perfil">Perfil de <?= $usuario['Nome']; ?></h2>

    <div class="grid dados-perfil">
        <div class="row">
            <div class="col col-md-5 mb-3">
                <div class="card z-depth-2">
                    <div class="card-header default-color white-text">Dados Pessoais</div>
                    <div class="card-body">
                        <h5 class="nome-perfil"><?= $usuario['Nome']; ?></h5>
                        <p class="texto-dados"><?= $usuario['Telefone']; ?></p>
                        <p class="texto-dados"><?= $usuario['CPF']; ?></p>
                        <p class="texto-dados"><?= $usuario['Email']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col col-md-7 mb-3">
                <div class="card z-depth-2">
                    <div class="card-header default-color white-text">Endereço</div>
                    <div class="card-body">
                        <h5 class="nome-perfil"><?= $usuario['Nome']; ?></h5>
                        <p class="texto-dados"><?= $usuario['Endereco']; ?></p>
                        <p class="texto-dados"><?php echo $usuario['Cidade'] . ' - ' . $usuario['Estado'] ?></p>
                        <p class="texto-dados"><?= $usuario['CEP']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card z-depth-2" id="cartao-grupos-usuario">
                    
                    <div class="card-header default-color-dark white-text">
                        <div class="row">
                            <div class="col-sm col-md col-lg">Grupos</div>
                            <div class="col-sm-2 col-md-3 col-lg-2">
                                <button class="btn default-color botao-pequeno btn-criar-grupo btn-block" style="float: right;" data-toggle="modal" data-target="#modal-criar-grupo" data-username="<?= $usuario['Usuario']; ?>">criar grupo</button>
                                <div class="adicional" style="display: none; float: right;"></div>
                            </div>
                            <div class="col-sm-2 col-md-4 col-lg-3"><button class="btn default-color botao-pequeno btn-recarregar-grupos btn-block" style="float: right;" username-usuario="<?= $usuario['Usuario'] ?>"><i class="fas fa-sync-alt" id="icone-recarregar"></i> recarregar grupos</button></div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="container">
                            <?php
                                $grupos = recuperar_grupos($conexao, $usuario['Usuario']);
                                if (count($grupos) > 0) {
                            ?>
                                <table class="table table-hover">
                                    <thead style="font-weight: bold;">
                                        <tr class="row">
                                            <th class="col-sm-4 thead-grupos">Nome</th>
                                            <th class="col-sm-3 thead-grupos">Data Criação</th>
                                            <th class="col-sm-3 thead-grupos">Número Membros</th>
                                            <th class="col-sm-2 thead-grupos">Visualizar</th>
                                        </tr>
                                    </thead>
                                    <tbody id="grupos-usuario">

                                    <?php foreach ($grupos AS $grupo) { ?>
                                        <tr class="row">
                                            <td class="col-sm-4"><?= $grupo['Nome']; ?></td>
                                            <td class="col-sm-3"><?= date("d/m/Y h:m", strtotime($grupo['Data_Criacao'])); ?></td>
                                            <td class="col-sm-3"><?= $grupo['Numero_Membros']; ?></td>
                                            <td class="col-sm-2">
                                                <button class="btn btn-info botao-pequeno btn-membros" id="<?= $grupo['ID']; ?>" username="<?= $usuario['Usuario']; ?>">Membros</button>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                            <?php
                                }
                                else {
                            ?>
                                    <div class="alert alert-danger" role="alert">Você não está em nenhum grupo</div>
                            <?php
                                }
                            ?>
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

    $(document).ready(function() {

        var url = window.location.href;
        var id = url.substring(url.lastIndexOf("#") + 1);
        
        if (id == "cartao-grupos-usuario") {
            $(".btn-criar-grupo").effect( "shake", "slow" );

            var largura = $(".btn-criar-grupo").width();

            $(".adicional").css({
                'display': 'inline'
            });
            $(".adicional").html('<div class="arrow bounce"><a class="fa fa-arrow-down fa-2x text-black-50" href="#"></a></div>');
            $(".arrow").css({
                'position': 'absolute',
                'z-index': '1',
                'margin-left': largura/2 + "px",
                'top': '0'
            });
        }

        $(window).resize(function() {
            if ($(window).width() <= 500) {
                $('.btn-criar-grupo').addClass("btn-block mt-2");
                $('.btn-recarregar-grupos').addClass("btn-block mt-2");
            } else {
                $('.btn-criar-grupo').removeClass("btn-block mt-2");
                $('.btn-recarregar-grupos').removeClass("btn-block mt-2");
            }
        });

        $('#modal-criar-grupo').on('show.bs.modal', function(event) {
            // Recupera as informacoes do botao
            var botao = $(event.relatedTarget);
            var username = botao.data('username');

            var modal = $(this);
            modal.find('#criar-username').val(username);
        });

                
    });


    /* ===========================================================================================================
    ===================================== PREENCHE MODAL MEMBROS GRUPO COM AJAX ==================================
    ============================================================================================================== */

    $(document).on('click', '.btn-membros', function() {
        var id_grupo = $(this).attr("id");
        var username = $(this).attr("username");            

        $.ajax({
            url: "modal-membros-grupo.php",
            method: "post",
            data: {
                id_grupo: id_grupo,
                username: username
            },
            success: function(data) {
                $("#membros-grupo").html(data);
                $("#modal-membros-grupo").modal("show");
                $('.input-usuario').select2({
                    ajax: {
                        url: "scripts/busca-usuario.php",
                        type: "post",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                busca: "sim",
                                texto: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });
            }
        });
    });


    /* ===========================================================================================================
    ========================================= RECARREGA OS GRUPOS COM AJAX =======================================
    ============================================================================================================== */

    $(".btn-recarregar-grupos").click(function() {
        var icone = document.querySelector("#icone-recarregar");
        var username = $(this).attr('username-usuario');
        
        icone.classList.add('fa-spin');

        setTimeout(function() {
            icone.classList.remove('fa-spin');
        }, 1000);

        $.ajax({
            url: "scripts/recarregar-grupos.php",
            method: "post",
            data: {
                username: username
            },
            success: function(retorno) {
                $('#grupos-usuario').html(retorno);                    
            }
        });

    });


    /* ===========================================================================================================
    ===================================== REALIZA BUSCA POR USERNAMES NO BD ======================================
    ========================================= UTILIZA O PLUGIN select2 ===========================================
    ============================================================================================================== */
    
    $('.input-usuario').select2({
        ajax: {
            url: "scripts/busca-usuario.php",
            type: "post",
            dataType: "json",
            delay: 250,
            data: function(params) {
                return {
                    busca: "sim",
                    texto: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });


    /* ===========================================================================================================
    ===================================== REALIZA REMOÇÃO DE UM MEMBRO NO GRUPO ==================================
    ============================================= E RECARREGA O MODAL ============================================
    ============================================================================================================== */
    
    $(document).on('mouseover', '.btn-remover-membro', function(){
        
        var id_grupo = $(this).attr("id-grupo");
        var username_usuario = $(this).attr('username-usuario');
        var usuario = $(this).attr("username-membro");

        
        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            onConfirm: function() {
                // Caso o usuário pressione 'Sim'
                $.ajax({
                    url: "modal-membros-grupo.php",
                    method: "post",
                    data: {
                        remover: "sim",
                        id_grupo: id_grupo,
                        usuario: usuario,
                        username: username_usuario
                    },
                    dataType: "html",
                    success: function(retorno) {
                        
                        $('#membros-grupo').html(retorno);
                        $('.input-usuario').select2({
                            ajax: {
                                url: "scripts/busca-usuario.php",
                                type: "post",
                                dataType: "json",
                                delay: 250,
                                data: function(params) {
                                    return {
                                        busca: "sim",
                                        texto: params.term
                                    };
                                },
                                processResults: function(data) {
                                    return {
                                        results: data
                                    };
                                },
                                cache: true
                            }
                        });
                    }
                });
            },
            onCancel: function() {
                // Caso o usuário pressione 'Não'
            }
            // other options
        });


    });


    /* ===========================================================================================================
    ====================================== REALIZA ADIÇÃO MAIS MEMBROS NO GRUPO ==================================
    ============================================= E RECARREGA O MODAL ============================================
    ============================================================================================================== */

    $(document).on('click', '.btn-adicionar-membros', function() {

        // Recupera os IDs dos usuários a serem adicionados
        var select = $('#select2-usuarios').val();
        var id_grupo = $(this).attr('id-grupo');        
        var username = $(this).attr('username-usuario');

        if (select) {
            $.ajax({
                url: "modal-membros-grupo.php",
                method: "post",
                data: {
                    adicionar: "sim",
                    id_grupo: id_grupo,
                    ids_adicionar: select,
                    username: username
                },
                dataType: "html",
                success: function(retorno) {
                    $('#membros-grupo').html(retorno);
                    $('.input-usuario').select2({
                        ajax: {
                            url: "scripts/busca-usuario.php",
                            type: "post",
                            dataType: "json",
                            delay: 250,
                            data: function(params) {
                                return {
                                    busca: "sim",
                                    texto: params.term
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data
                                };
                            },
                            cache: true
                        }
                    });
                }
            });
        }
        
        
    });


    /* ===========================================================================================================
    ========================================== BOTÃO PARA SAIR DO GRUPO ==========================================
    ============================================================================================================== */

    $(document).on('click', '.btn-sair-grupo', function() {

        var id_grupo = $(this).attr('id-grupo');        
        var username = $(this).attr('username-usuario');

        $.ajax({
            url: "modal-membros-grupo.php",
            method: "post",
            dataType: "json",
            data: {
                sair: "sim",
                id_grupo: id_grupo,
                usuario: username
            },
            success: function(retorno) { 
                if (retorno.quantidade == 0) {
                    $.post('scripts/remover-grupo.php', {
                        remover_grupo: "sim",
                        id: id_grupo
                    }, function(data, status) {
                        location.href = "perfil-usuario.php";
                    });
                }
                else {
                    location.href = "perfil-usuario.php";
                }
            }
        });

    });



</script>