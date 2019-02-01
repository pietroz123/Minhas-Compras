<?php
    if (isset($_POST['id_grupo'])) {


        include $_SERVER['DOCUMENT_ROOT'].'/database/conexao.php';        
        include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-grupos.php';
        include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';

        $id_grupo = $_POST['id_grupo'];


        // Verifica se a requisicao foi para remover um membro do grupo
        if (isset($_POST['remover']) && $_POST['remover'] == "sim") {

            $username = $_POST['usuario'];

            remover_membro($conexao, $id_grupo, $username);

        }

        // Verifica se a requisicao foi para adicionar um membro ao grupo
        elseif (isset($_POST['adicionar']) && $_POST['adicionar'] == "sim") {

            $ids_adicionar = $_POST['ids_adicionar'];
            foreach ($ids_adicionar as $id_adicionar) {
                $usuario = buscar_usuario_id($conexao, $id_adicionar);
                adicionar_membro($conexao, $id_grupo, $usuario['Usuario']);
            }

        }

        // Verifica se a requisicao foi para sair do grupo
        elseif (isset($_POST['sair']) && $_POST['sair'] == "sim") {

            $username = $_POST['usuario'];

            remover_membro($conexao, $id_grupo, $username);
            die();            

        }


        $grupo = recuperar_grupo($conexao, $id_grupo);
        $membros = recuperar_membros($conexao, $id_grupo);
?>

    <div class="modal-content">
        <div class="modal-header">
            <div class="grid informacoes-grupo" style="width: 100%;">
                <div class="row">
                    <div class="col"><h3 class="titulo-membros"><?= $grupo['Nome']; ?></h3></div>
                </div>
                <div class="row">
                    <div class="col-sm-6"><h6 id="data-criacao">Data de Criação:</h6></div>
                    <div class="col-sm-6"><h6 id="data"><?= $grupo['Data_Criacao']; ?></h6></div>
                </div>
                <div class="row">
                    <div class="col-sm-6"><h6 id="numero-membros">Número de Membros:</h6></div>
                    <div class="col-sm-6"><h6 id="numero"><?= $grupo['Numero_Membros']; ?></h6></div>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="container">
                <table class="table table-hover text-left">
                    <thead>
                        <tr class="row">
                            <th class="col-sm-5">Nome</th>
                            <th class="col-sm-4">Desde</th>
                            <th class="col-sm-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($membros as $membro) {
                        ?>
                        <tr class="row">
                            <td class="col-sm-5"><i class="fas fa-user mr-2 float-left"></i><?= $membro['Nome']; ?></td>
                            <td class="col-sm-4"><?= date("d/m/Y h:m", strtotime($membro['Membro_Desde'])); ?></td>
                    <?php
                        if ($membro['Usuario'] == $_POST['username']) {
                    ?>
                            <td class="col-sm-3 text-right"><button class="btn btn-danger botao-pequeno btn-remover-membro w-75" id-grupo="<?= $grupo['ID']; ?>" username-usuario="<?= $_POST['username']; ?>" username-membro="<?= $membro['Usuario']; ?>" data-toggle="confirmation" data-singleton="true">sair</button></td>
                    <?php
                        } elseif (isAdmin($conexao, $grupo['ID'], $_POST['username'])) {
                    ?>
                            <td class="col-sm-3 text-right"><button class="btn btn-danger botao-pequeno btn-remover-membro w-75" id-grupo="<?= $grupo['ID']; ?>" username-usuario="<?= $_POST['username']; ?>" username-membro="<?= $membro['Usuario']; ?>" data-toggle="confirmation" data-singleton="true"><i class="fas fa-times"></i></button></td>
                    <?php
                        }
                    ?>
                        </tr>
                        <?php
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="container mt-3 mb-3">
                <div class="row">
                    <div class="col-sm">
                        <label for="select2" class="font-weight-bold left">Adicionar usuários</label>
                        <select class="form-control input-usuario" id="select2-usuarios" name="usernames[]" multiple="multiple" style="width: 100%;">
                
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <button class="btn btn-success btn-block btn-adicionar-membros" id-grupo="<?= $grupo['ID']; ?>" username-usuario="<?= $_POST['username']; ?>">adicionar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php
                if (isAdmin($conexao, $grupo['ID'], $_POST['username'])) {
            ?>
                <form action="scripts/remover-grupo.php" method="post">
                    <input type="hidden" name="id" value="<?= $grupo['ID']; ?>">
                    <button type="submit" name="submit-remover-grupo" class="btn btn-danger float-right">remover grupo</button>
                </form>
            <?php
                }
            ?>
            <button class="btn btn-danger btn-sair-grupo float-left" id-grupo="<?= $grupo['ID']; ?>" username-usuario="<?= $_POST['username']; ?>">sair do grupo</button>
        </div>
    </div>

<?php
    }