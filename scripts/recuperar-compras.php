<?php
    include $_SERVER['DOCUMENT_ROOT'].'/database/dbconnection.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-grupos.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes.php';
    include $_SERVER['DOCUMENT_ROOT'].'/config/sessao.php';

    include $_SERVER['DOCUMENT_ROOT'].'/persistence/CompraDAO.php';
    include $_SERVER['DOCUMENT_ROOT'].'/persistence/GrupoDAO.php';


    // =======================================================
    // Recuperar todas as compras do usuário
    // =======================================================

    if ( isset($_POST['requisicao']['todas']) && $_POST['requisicao']['todas'] = 'sim' ) {

        $cdao = new CompraDAO();
        $json = $cdao->recuperarComprasUsuarioJSON($dbconn, $_SESSION['login-id-comprador'], $_POST);
        
        echo $json;

    }


    // =======================================================
    // Recuperar todas as compras de um grupo
    // =======================================================

    if ( isset($_POST['requisicao']['id_grupo']) ) {

        $id_grupo = $_POST['requisicao']['id_grupo'];

        $cdao = new CompraDAO();
        $json = $cdao->recuperarComprasGrupoJSON($dbconn, $id_grupo, $_POST);
        
        echo $json;
        
    }


    // =======================================================
    // Recuperar as compras da página de Busca
    // =======================================================

    if (isset($_POST['requisicao']['buscar_compras']) && $_POST['requisicao']['buscar_compras'] == "sim") {

        // Recupera os dados da requisição
        $palavra_chave  = $_POST['requisicao']['palavra_chave'];
        $data_range     = $_POST['requisicao']['data_range'];
        $id_comprador   = $_POST['requisicao']['id_comprador'];

        $cdao = new CompraDAO();

        // Verifica se o usuário selecionou 'Todos'
        if ($id_comprador == 0)
            $json = $cdao->recuperarComprasPermitidasJSON($dbconn, $palavra_chave, $data_range, $_POST);
        // Ou um comprador específico
        else
            $json = $cdao->recuperarComprasBuscaJSON($dbconn, $palavra_chave, $data_range, $id_comprador, $_POST);
        
        echo $json;

    }

    // =======================================================
    // Recuperas a soma dos valores das compras
    // =======================================================

    if (isset($_POST['recuperar_soma']) && $_POST['recuperar_soma'] == "sim") {

        // Recupera os dados da requisição
        $palavra_chave  = $_POST['requisicao']['palavra_chave'];
        $data_range     = $_POST['requisicao']['data_range'];
        $id_comprador   = $_POST['requisicao']['id_comprador'];

        $cdao = new CompraDAO();

        // Verifica se o usuário selecionou 'Todos'
        if ($id_comprador == 0)
            $soma = $cdao->recuperarComprasPermitidasJSON($dbconn, $palavra_chave, $data_range, $_POST, 1);
        // Ou um comprador específico
        else
            $soma = $cdao->recuperarComprasBuscaJSON($dbconn, $palavra_chave, $data_range, $id_comprador, $_POST, 1);
        
        echo $soma;

    }


    // =======================================================
    // Recuperar as compras de determinado dia
    // =======================================================
    
    if (isset($_POST['data_compra'])) {

        $data_compra = $_POST['data_compra'];

        $email = $_SESSION['login-email'];
        $sql = "SELECT c.Observacoes, c.Valor, c.Desconto, c.Forma_Pagamento, c.Imagem, c.Comprador_ID
                FROM compras c
                WHERE c.Data = '$data_compra'
                AND c.Comprador_ID = (
                    SELECT co.ID
                    FROM compradores co
                    WHERE co.Email = '$email'
                );";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $retorno = '';
        $retorno .= '<table class="table table-hover datatable-compras" id="tabela-compras">
    
            <thead class="thead-dark">
                <tr>
                    <th class="th-sm">Observacoes</th>
                    <th class="th-sm">Valor</th>
                    <th class="th-sm">Pagamento</th>
                </tr>
            </thead>
        
            <tbody id="compras-datatable">';

            // Preenche com as compras
            $compras = array();
            while ($compra = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($compras, $compra);
                $retorno .= '<tr>
                    <td>'.$compra['Observacoes'].'</td>
                    <td>'.$compra['Valor'].'</td>
                    <td>'.$compra['Forma_Pagamento'].'</td>
                </tr>';
            }

            $retorno .= '</tbody></table>';

        echo $retorno;

    }