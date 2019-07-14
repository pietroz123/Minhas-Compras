<?php
    include $_SERVER['DOCUMENT_ROOT'].'/cabecalho.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';

    mostra_alertas();
    
    if (!usuario_esta_logado()) {
?>

        <style>
            .conteudo-principal {
                padding-top: 0px;
                padding-bottom: 0px;
            }
            #container-principal {
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-height: 100vh;
            }
        </style>

        <h1>Bem Vindo ao Controle de Compras</h1>

        <p class="bem-vindo-index">O <mark>Controle de Compras</mark> é uma aplicação web que permite o armazenamento de <mark>compras</mark>. É possível armazenar as observações, data, valor, desconto, forma de pagamento e até a nota fiscal de uma compra. Além disso, o sistema permite associar uma compra a um <mark>comprador</mark>, que pode ser o próprio usuário ou um amigo/familiar devidamente cadastrado.</p>

        <div class="alert alert-warning">Efetue o login ou o cadastro à partir do Menu de Navegação.</div>
        
        <div class="redes">
            <ul class="list-inline">
                <li class="list-inline-item"><a href="https://github.com/pietroz123"><img src="img/github.png" class="medium-icon" alt="Github"></a></li>
                <li class="list-inline-item"><a href="https://www.linkedin.com/in/pietro-zuntini-b23506140/"><img src="img/linkedin.png" class="medium-icon" alt="Linkedin"></a></li>
                <li class="list-inline-item"><a href="https://gitlab.com/pietroz123"><img src="img/gitlab.png" class="medium-icon" alt="Gitlab"></a></li>
            </ul>
        </div>

<?php
    } else {
?>

        <h3 class="text-left titulo-site">Bem Vindo de Volta, <?= $_SESSION['login-nome'] ?></h3>
        <hr class="mb-5">

        <div class="alert alert-info">Logado como <?= usuario_logado(); ?></div>

        <!-- Cartão de informações -->
        <div class="card">
            <div class="card-body p-2">

                <a role="button" class="btn-informacoes-toggle">
                    <div class="card-header elegant-color-dark py-4 white-text text-uppercase">
                        <div class="card-title" id="titulo-informacoes">Informações <i class="fas fa-chevron-down rotate"></i></div>
                    </div>
                </a>
                
                <section id="informacoes">
                    <div id="informacoes" class="d-flex align-content-center flex-wrap">

                        <?php
                            if (admin()) {
                        ?>

                            <!--
                            =======================================================
                            Nível: Administrador
                            =======================================================
                            -->

                            <?php
                                // Recupera o número de usuários
                                $sql = "SELECT * FROM `usuarios`";
                                $stmt = $dbconn->prepare($sql);
                                $stmt->execute();
                                $nUsuarios = $stmt->rowCount(); 

                            ?>
                            <article class="cartao-informacao">
                                <b class="cartao-informacao-titulo">Número de Usuários</b>
                                <div class="cartao-informacao-desc"><?= $nUsuarios; ?></div>
                            </article>

                            <?php
                                // Recupera o número de compras
                                $sql = "SELECT * FROM `compras`";
                                $stmt = $dbconn->prepare($sql);
                                $stmt->execute();
                                $nCompras = $stmt->rowCount(); 
                                
                            ?>
                            <article class="cartao-informacao">
                                <b class="cartao-informacao-titulo">Número de Compras</b>
                                <div class="cartao-informacao-desc"><?= $nCompras; ?></div>
                            </article>

                            <?php
                                // Recupera o número de grupos
                                $sql = "SELECT * FROM `grupos`";
                                $stmt = $dbconn->prepare($sql);
                                $stmt->execute();
                                $nGrupos = $stmt->rowCount(); 
                                
                            ?>
                            <article class="cartao-informacao">
                                <b class="cartao-informacao-titulo">Número de Grupos</b>
                                <div class="cartao-informacao-desc"><?= $nGrupos; ?></div>
                            </article>

                            <?php
                                // Recupera o número de usuários não autenticados
                                $sql = "SELECT * FROM `usuarios` WHERE `Autenticado` = 0";
                                $stmt = $dbconn->prepare($sql);
                                $stmt->execute();
                                $nRequisicoes = $stmt->rowCount(); 
                                
                            ?>
                            <article class="cartao-informacao">
                                <b class="cartao-informacao-titulo">Número de Requisições</b>
                                <div class="cartao-informacao-desc"><?= $nRequisicoes; ?></div>
                                <a href="perfil-usuario.php#cartao-requisicoes" class="botao botao-pequeno btn btn-light">visualizar</a>
                            </article>

                            <?php
                                // Recupera o número de backups
                                $nBackups = 0;
                                foreach (glob("../private/backups/banco/*.sql") as $filename) {
                                    $nBackups++;
                                }
                                
                            ?>
                            <article class="cartao-informacao">
                                <b class="cartao-informacao-titulo">Número de Backups</b>
                                <div class="cartao-informacao-desc"><?= $nBackups ?></div>
                                <a href="index.php#cartao-backups" class="botao botao-pequeno btn btn-light">visualizar</a>
                            </article>

                        <?php
                            }
                        ?>

                        <!--
                        =======================================================
                        Nível: Usuário                    
                        =======================================================
                        -->

                        <?php
                            // Recupera o número de grupos do usuário
                            $sql = "SELECT * FROM grupo_usuarios gu WHERE gu.Username = '{$_SESSION['login-username']}'";
                            $stmt = $dbconn->prepare($sql);
                            $stmt->execute();
                            $nGrupos = $stmt->rowCount(); 
                            
                        ?>
                        <article class="cartao-informacao">
                            <b class="cartao-informacao-titulo">Meus Grupos</b>
                            <div class="cartao-informacao-desc"><?= $nGrupos; ?></div>
                            <?php 
                                if ($nGrupos == 0) { 
                            ?>                            
                                    <a href="perfil-usuario.php#cartao-grupos-usuario" class="botao botao-pequeno btn btn-default">criar um grupo?</a>
                            <?php 
                                } else { 
                            ?>
                                    <a href="perfil-usuario.php#container-tabela-grupos" class="botao botao-pequeno btn btn-light">visualizar</a>
                            <?php
                                }
                            ?>
                        </article>

                        <?php
                            // Recupera o número de compras
                            $sql = "SELECT * FROM `compras` WHERE `comprador_id` = {$_SESSION['login-id-comprador']}";
                            $stmt = $dbconn->prepare($sql);
                            $stmt->execute();
                            $nCompras = $stmt->rowCount(); 
                            
                        ?>
                        <article class="cartao-informacao">
                            <b class="cartao-informacao-titulo">Minhas Compras</b>
                            <div class="cartao-informacao-desc"><?= $nCompras; ?></div>
                            <?php 
                                if ($nCompras == 0) { 
                            ?>                            
                                    <a href="formulario-compra.php" class="botao botao-pequeno btn btn-default">adicionar uma compra?</a>
                            <?php 
                                } else { 
                            ?>
                                    <a href="compras.php#tabela-compras" class="botao botao-pequeno btn btn-light">visualizar</a>
                            <?php
                                }
                            ?>
                        </article>

                    </div>
                
                </section>

            </div>
        </div>

        <?php
            if (admin()) {
        ?>
            <div class="card mt-5" id="cartao-backups">
                <div class="card-body p-2">
                    
                    <a role="button" class="btn-backup-toggle">
                        <div class="card-header elegant-color-dark py-4 white-text text-uppercase">
                            <div class="card-title" id="titulo-informacoes">Backup de arquivos do banco <i class="fas fa-chevron-down rotate"></i></div>
                        </div>
                    </a>

                    <div class="elegant-color p-3" id="backups">

                        <!-- Realizar Backup -->
                        <!-- <form action="backup/myphp-backup.php"> -->
                            <button class="btn btn-success botao mb-4 btn-backup">clique aqui para realizar backup</button>
                            <div id="resultado-backup" class="bg-white mb-3" style="display: none;">
                                <div class="container p-2">
                                    <div class="row">
                                        <div class="col opcao-backup-intro text-uppercase"><p>Realizar Backup de:</p></div>
                                    </div>

                                    <div class="row" id="tabelas-backup">

                                        <!-- Checkboxes das Tabelas -->
                                        <form class="col d-flex flex-column text-left ml-4" id="formTabelas">
                                            <div class="opcao-backup-titulo">TABELAS:</div>
                                            <div class="custom-control custom-checkbox chk-tabelas opcao-backup">
                                                <input type="checkbox" class="custom-control-input" name="chk_tb" id="chk_tb_todas" value="todas">
                                                <label class="custom-control-label" for="chk_tb_todas">Todas</label>
                                            </div>
                                            <?php
                                                $sql = "SHOW TABLES";
                                                $stmt = $dbconn->prepare($sql);
                                                $stmt->execute();
                                                $tables = array();
                                                while ($table = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                                    <div class="custom-control custom-checkbox chk-tabelas opcao-backup">
                                                        <input type="checkbox" class="custom-control-input" name="chk_tb" id="chk-<?= $table['Tables_in_'.$banco] ?>" value="<?= $table['Tables_in_'.$banco] ?>">
                                                        <label class="custom-control-label" for="chk-<?= $table['Tables_in_'.$banco] ?>"><?= strtoupper($table['Tables_in_'.$banco]) ?></label>
                                                    </div>
                                            <?php
                                                }
                                            ?>
                                        </form>
                                        <!-- Checkboxes das Tabelas -->

                                        <!-- Checkboxes das Opções -->
                                        <form class="col d-flex flex-column text-left ml-4" id="formOpcoes">
                                            <div class="opcao-backup-titulo">INFORMAÇÕES:</div>
                                            <div class="custom-control custom-checkbox chk-informacoes opcao-backup">
                                                <input type="checkbox" class="custom-control-input" name="chk_info" id="chk_info_todas" value="todas">
                                                <label class="custom-control-label" for="chk_info_todas">Todas</label>
                                            </div>
                                            <div class="custom-control custom-checkbox chk-informacoes opcao-backup">
                                                <input type="checkbox" class="custom-control-input" name="chk_info" id="chk-dados" value="dados">
                                                <label class="custom-control-label" for="chk-dados">Dados</label>
                                            </div>
                                            <div class="custom-control custom-checkbox chk-informacoes opcao-backup">
                                                <input type="checkbox" class="custom-control-input" name="chk_info" id="chk-estrutura" value="estrutura">
                                                <label class="custom-control-label" for="chk-estrutura">Estrutura</label>
                                            </div>
                                        </form>
                                        <!-- Checkboxes das Opções -->

                                    </div>

                                    <hr>

                                    <!-- Botão de OK -->
                                    <div class="row">
                                        <div class="col">
                                            <button class="btn btn-success float-right" id="btn-ok-backup" type="submit" name="submit-backup">ok</button>
                                            <button class="btn btn-danger float-right" id="btn-cancelar-backup">cancelar</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        <!-- </form> -->



                        <h4 class="white-text text-left py-2">Ultimos Backups</h4>
                        
                        <div id="ultimos-backups" class="bg-light py-4">
                            <!-- Preenchido com JQuery -->
                        </div>
                        <!-- ultimos-backups -->

                        <!-- Modal Backup Arquivo -->
                        <div class="modal fade" id="modal-backup-arquivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                            <!-- Preenchido com AJAX -->
                        </div>
                        
                    </div>

                </div>
            </div>

            <div class="card mt-5" id="cartao-backup-images">
                <div class="card-body p-2">
                    
                    <a role="button" class="btn-backup-imagens-toggle">
                        <div class="card-header elegant-color-dark py-4 white-text text-uppercase">
                            <div class="card-title" id="titulo-informacoes">Backup de imagens <i class="fas fa-chevron-down rotate"></i></div>
                        </div>
                    </a>

                    <div class="elegant-color p-3" id="backup-imagens">
                        
                        <div class="container">
                            <div class="row">
                            <?php
                                $contImg = 0;
                                foreach (glob('../private/uploads/compras/*.*') as $arquivo) {
                                    $contImg++;
                                }
                                if ($contImg == 0) {
                            ?>
                                <div class="col"><div class="alert alert-info">Nenhuma imagem disponível para download</div></div>
                            <?php
                                }
                                else {
                            ?>
                                <div class="col-12 col-sm text-white d-flex justify-content-center flex-column align-items-center">Número de imagens disponíveis: <?= $contImg ?></div>
                                <div class="col-12 col-sm">
                                    <!-- Realizar Backup -->
                                    <form action="backup/backup-imagens.php" method="POST">
                                        <button type="submit" name="submit-download-imagens" class="btn btn-default" id="btn-backup-imagens">fazer backup das notas fiscais</button>
                                    </form>
                                </div>
                            <?php
                                }
                            ?>
                            </div>
                        </div>

                        
                    </div>

                </div>
            </div>


        <?php
            }
        ?>

<?php
    }
?>

            </div>
            <!-- Div container -->

            <?php
                if (!usuario_esta_logado()) {
            ?>

            <section id="recursos" class="bg-white black-text p-5">

                <h4 class="mb-5 titulo-recursos">Recursos</h4>
        
                <div class="d-flex justify-content-center align-items-center align-items-md-baseline flex-column flex-md-row">
                    <div class="recurso">
                        <div class="container">
                            <div class="recurso-icone"><i class="fas fa-shopping-bag fa-3x"></i></i></div>
                            <div class="recurso-texto mt-3"><p>Armazene suas compras</p></div>
                        </div>
                    </div>
                    
                    <div class="recurso">
                        <div class="container">
                            <div class="recurso-icone"><i class="fas fa-users fa-3x"></i></div>
                            <div class="recurso-texto mt-3"><p>Crie grupos e visualize as compras de seus amigos e familiares</p></div>
                        </div>
                    </div>
                    
                    <div class="recurso">
                        <div class="container">
                            <div class="recurso-icone"><i class="fas fa-chart-bar fa-3x"></i></div>
                            <div class="recurso-texto mt-3"><p>Gere relatórios</p></div>
                        </div>
                    </div>
                    
                    <div class="recurso">
                        <div class="container">
                            <div class="recurso-icone"><i class="fas fa-search fa-3x"></i></div>
                            <div class="recurso-texto mt-3"><p>Realize buscas pelas suas compras</p></div>
                        </div>
                    </div>
                </div>

            </section>
            <!-- section.recursos -->

            <?php
                }
            ?>


        </div>

<?php
    include $_SERVER['DOCUMENT_ROOT'].'/rodape.php';
?>


<script>

    // =======================================================
    // Toggle dos cartões
    // =======================================================

    $('.btn-informacoes-toggle').click(function() {
        $('#informacoes').slideToggle();
        $('.btn-informacoes-toggle i').toggleClass("down");
    });
    $('.btn-backup-toggle').click(function() {
        $('#backups').slideToggle();
        $('.btn-backup-toggle i').toggleClass("down");
    });
    $('.btn-backup-imagens-toggle').click(function() {
        $('#backup-imagens').slideToggle();
        $('.btn-backup-imagens-toggle i').toggleClass("down");
    });



    $(document).ready(function() {

        // Recupera os backups
        $('#ultimos-backups').load('backup/recupera-backups.php');

    });

    // Salva as opcoes do backup para utilizacao posterior
    var opcoesBackup = $('#resultado-backup').html();

    
    // =======================================================
    // Botão Realizar Backup Arquivos
    // =======================================================


    $(document).on('click', '.btn-backup', function() {
        $('#resultado-backup').slideDown();
    });

    $(document).on('click', '#btn-cancelar-backup', function() {
        $('#resultado-backup').slideUp();
    });

    $(document).on('click', '#btn-ok-backup', function() {

        var tabelas = [];
        $.each($("input[name='chk_tb']:checked"), function() {
            tabelas.push($(this).val());
        });

        var opcoes = [];
        $.each($("input[name='chk_info']:checked"), function() {
            opcoes.push($(this).val());
        });

        $.ajax({
            url: 'backup/myphp-backup.php',
            method: 'POST',
            data: {
                backup: "sim",
                tabelas: tabelas,
                opcoes: opcoes
            },
            success: function(retorno) {
                $('#resultado-backup').html(retorno);
                toastr.success('Backup realizado com sucesso!', '', {
                    positionClass: "toast-top-right"
                });
                
                // Recupera os backups
                $('#ultimos-backups').load('backup/recupera-backups.php');
            },
            error: function(retorno) {
            }
        });
    });



    // ============================================================
    // Verificação das Checkbox - Javascript & JQuery Jon Ducket
    // ============================================================

    // Helper function to add an event listener
    function addEvent (el, event, callback) {
        if ('addEventListener' in el) {                  // If addEventListener works
            el.addEventListener(event, callback, false);   // Use it
        } else {                                         // Otherwise
            el['e' + event + callback] = callback;         // CreateIE fallback
            el[event + callback] = function () {
            el['e' + event + callback](window.event);
            };
            el.attachEvent('on' + event, el[event + callback]);
        }
    }

    // Para o form das Tabelas

    var formTabelas = document.getElementById('formTabelas');
    var elementosTabelas = formTabelas.elements;
    var opcoesTabelas = elementosTabelas.chk_tb;
    var inputTodasTabelas = document.getElementById('chk_tb_todas');

    function updateAllTabelas() {
        for (let i = 0; i < opcoesTabelas.length; i++) {
            opcoesTabelas[i].checked = inputTodasTabelas.checked;
        }
    }
    addEvent(inputTodasTabelas, 'change', updateAllTabelas);

    function clearAllOptionsTabelas(e) {
        var target = e.target || e.srcElement;
        if (!target.checked) {
            inputTodasTabelas.checked = false;
        }
    }
    for (let i = 0; i < opcoesTabelas.length; i++) {
        addEvent(opcoesTabelas[i], 'change', clearAllOptionsTabelas);
        
    }


    // Para o form das Opcoes

    var formOpcoes = document.getElementById('formOpcoes');
    var elementosOpcoes = formOpcoes.elements;
    var opcoesOpcoes = elementosOpcoes.chk_info;
    var inputTodasOpcoes = document.getElementById('chk_info_todas');

    function updateAllOpcoes() {
        for (let i = 0; i < opcoesOpcoes.length; i++) {
            opcoesOpcoes[i].checked = inputTodasOpcoes.checked;
        }
    }
    addEvent(inputTodasOpcoes, 'change', updateAllOpcoes);

    function clearAllOptionsOpcoes(e) {
        var target = e.target || e.srcElement;
        if (!target.checked) {
            inputTodasOpcoes.checked = false;
        }
    }
    for (let i = 0; i < opcoesOpcoes.length; i++) {
        addEvent(opcoesOpcoes[i], 'change', clearAllOptionsOpcoes);
        
    }




    // =======================================================
    // Botao de fechar progresso do backup
    // =======================================================

    $(document).on('click', '#btn-fechar-progresso', function() {

        $('#progresso-backup').remove();
        // Reseta o conteudo do resultado-backup
        $('#resultado-backup').html(opcoesBackup);
        $('#resultado-backup').toggle();

    });


    // =======================================================
    // Visualizar e Remover ARQUIVOS
    // =======================================================

    // Visualizar
    $(document).on('click', '#btn-visualizar-backup', function() {

        var nomeArquivo = $(this).attr('nome-arquivo');

        $.ajax({
            url: '/backup/modal-backup-arquivo.php',
            method: 'POST',
            data: {
                visualizar: "sim",
                nomeArquivo: nomeArquivo
            },
            success: function(retorno) {
                $('#modal-backup-arquivo').html(retorno);
                PR.prettyPrint();   // É necessário chamar o método novamente para funcionar
                $('#modal-backup-arquivo').modal("show");
            },
            error: function(retorno) {
                console.log('Error');
                console.log(retorno);
            }
        });
        

    });

    // Remover
    $(document).on('mouseover', '#btn-remover-backup', function() {

        var nomeArquivo = $(this).attr('nome-arquivo');

        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            onConfirm: function() {
                // Caso o usuário pressione 'Sim'
                
                // 1) Script de remoção do arquivo
                $.ajax({
                    url: 'backup/remover-backup.php',
                    method: 'POST',
                    data: {
                        remover: "sim",
                        nome_arquivo: nomeArquivo
                    },
                    dataType: "json",
                    success: function(retorno) {

                        // 2) Mostra toastr de remoção concluida
                        toastr.success('Remoção do arquivo com sucesso!', '', {
                            positionClass: "toast-top-right"
                        });

                        // 3) Atualiza os backups
                        $('#ultimos-backups').load('backup/recupera-backups.php');

                    },
                    error: function(retorno) {

                        // 1) Mostra mensagem de erro
                        toastr.error('Erro na remoção do arquivo: '+retorno.responseText, '', {
                            positionClass: "toast-top-right"
                        });
                    }
                });
                
                


            },
            onCancel: function() {
                // Caso o usuário pressione 'Não'
            }
            // Outras opções
        });
        

    });



</script>