<?php

/**
 * Description of CompraDAO
 *
 * @author Pietro
 */
class CompraDAO {
    
    /**
     * Recupera todas as compras de um grupo
     * 
     * @param PDO $dbconn
     * @param int $id_grupo
     * 
     * @return array[Compra] $compras
     */
    public static function recuperarComprasGrupo($dbconn, $id_grupo) {
        
        $ids_compradores = GrupoDAO::recuperarCompradoresGrupo($dbconn, $id_grupo);

        $sql = 'SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (';

        foreach ($ids_compradores as $id) {
            $sql .= $id['ID'];
            if ($id != end($ids_compradores))
                $sql .= ',';
        }

        $sql .= ')';

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $compras = $stmt->fetchAll();

        return $compras;

    }


    /**
     * Recupera as compras de um usuário em formato JSON
     * 
     * @param PDO   $dbconn         : Conexão com o BD
     * @param int   $id_usuario     : ID do Usuário
     * @param array $post           : Requisição POST com todo o cabeçalho do DataTables ServerSide
     */
    function recuperarComprasUsuarioJSON($dbconn, $id_usuario, $post) {

        /**
         * Sem nenhum filtro (todas as compras)
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID = $id_usuario";

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        
        $qtd_total = count($rs);


        /**
         * COM LIMIT
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID = $id_usuario ";


        // Preenche a SQL de acordo com as variáveis do DataTable Server Side
        $sql .= $this->preencherSQL($post['search']['value'], $post['order'][0]['column'], $post['order'][0]['dir'], $post['length'], $post['start']);
        

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $compras = $stmt->fetchAll();


        /**
         * SEM LIMIT (mas com filtros)
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID = $id_usuario ";

        // =======================================================
        // Campo de busca
        // =======================================================

        if (!empty($post['search']['value']))
            $sql .= 'AND c.Observacoes LIKE \'%'.strtoupper($post['search']['value']).'%\' ';



        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        $qtd_filtrada = count($rs);



        // =======================================================
        // Cria o JSON
        // =======================================================
        
        $json = $this->criarJSON($compras, $post['draw'], $qtd_total, $qtd_filtrada);

        return $json;

    }


    /**
     * Recupera as compras de um grupo em formato JSON
     * 
     * @param PDO   $dbconn         : Conexão com o BD
     * @param int   $id_grupo       : ID do Grupo
     * @param array $post           : Requisição POST com todo o cabeçalho do DataTables ServerSide
     */
    function recuperarComprasGrupoJSON($dbconn, $id_grupo, $post) {

        $ids_compradores = GrupoDAO::recuperarCompradoresGrupo($dbconn, $id_grupo);

        /**
         * Sem nenhum filtro (todas as compras)
         */

        $sql = 'SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (';

        foreach ($ids_compradores as $id) {
            $sql .= $id['ID'];
            if ($id != end($ids_compradores))
                $sql .= ',';
        }

        $sql .= ')';

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();
        
        $qtd_total = count($rs);


        /**
         * COM LIMIT
         */

        $sql = 'SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (';

        foreach ($ids_compradores as $id) {
            $sql .= $id['ID'];
            if ($id != end($ids_compradores))
                $sql .= ',';
        }

        $sql .= ')';


        // Preenche a SQL de acordo com as variáveis do DataTable Server Side
        $sql .= $this->preencherSQL($post['search']['value'], $post['order'][0]['column'], $post['order'][0]['dir'], $post['length'], $post['start']);
        

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $compras = $stmt->fetchAll();


        /**
         * SEM LIMIT (mas com filtros)
         */

        $sql = 'SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras c
        JOIN compradores cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (';

        foreach ($ids_compradores as $id) {
            $sql .= $id['ID'];
            if ($id != end($ids_compradores))
                $sql .= ',';
        }

        $sql .= ')';

        // =======================================================
        // Campo de busca
        // =======================================================

        if (!empty($post['search']['value']))
            $sql .= 'AND c.Observacoes LIKE \'%'.strtoupper($post['search']['value']).'%\' ';



        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        $qtd_filtrada = count($rs);


        
        // =======================================================
        // Cria o JSON
        // =======================================================
        
        $json = $this->criarJSON($compras, $post['draw'], $qtd_total, $qtd_filtrada);

        return $json;

    }


    /**
     * Recupera as compras da página de busca em formato JSON
     * 
     * @param PDO       $dbconn         : Conexão com o BD
     * @param string    $palavra_chave  : Palavra ou frase chave da busca
     * @param string    $data_range     : Intervalo de data
     * @param int       $id_comprador   : ID do Comprador da requisição
     * @param array     $post           : Requisição POST com todo o cabeçalho do DataTables ServerSide
     */
    function recuperarComprasBusca($dbconn, $palavra_chave, $data_range, $id_comprador, $post) {

        // Separa o intervalo de datas em data de início e fim
        $datas = $data_range;
        if (!empty($datas)) {
            $datas = explode(' - ', $datas);
            $data_inicio = implode('-', array_reverse(explode('/', $datas[0])));
            $data_fim    = implode('-', array_reverse(explode('/', $datas[1])));
        }
        else {
            $data_inicio = '';
            $data_fim    = '';
        }


        /**
         * Sem nenhum filtro (todas as compras)
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador 
        FROM compras AS c 
        JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID 
        WHERE observacoes LIKE '%{$palavra_chave}%' ";
        
        // Verifica se tem um intervalo de datas
        if (!empty($data_inicio) && !empty($data_fim))
            $sql .= "AND data >= '{$data_inicio}' AND data <= '{$data_fim}' ";
            
        $sql .= "AND Comprador_ID = {$id_comprador} ";


        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        
        $qtd_total = count($rs);


        /**
         * COM LIMIT
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador 
        FROM compras AS c 
        JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID 
        WHERE observacoes LIKE '%{$palavra_chave}%' ";
        
        // Verifica se tem um intervalo de datas
        if (!empty($data_inicio) && !empty($data_fim))
            $sql .= "AND data >= '{$data_inicio}' AND data <= '{$data_fim}' ";
            
        $sql .= "AND Comprador_ID = {$id_comprador} ";
        

        // Preenche a SQL de acordo com as variáveis do DataTable Server Side
        $sql .= $this->preencherSQL($post['search']['value'], $post['order'][0]['column'], $post['order'][0]['dir'], $post['length'], $post['start']);
        

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $compras = $stmt->fetchAll();


        /**
         * SEM LIMIT (mas com filtros)
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador 
        FROM compras AS c 
        JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID 
        WHERE observacoes LIKE '%{$palavra_chave}%' ";
        
        // Verifica se tem um intervalo de datas
        if (!empty($data_inicio) && !empty($data_fim))
            $sql .= "AND data >= '{$data_inicio}' AND data <= '{$data_fim}' ";
            
        $sql .= "AND Comprador_ID = {$id_comprador} ";


        // =======================================================
        // Campo de busca
        // =======================================================

        if (!empty($post['search']['value']))
            $sql .= 'AND c.Observacoes LIKE \'%'.strtoupper($post['search']['value']).'%\' ';



        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        $qtd_filtrada = count($rs);


        
        // =======================================================
        // Cria o JSON
        // =======================================================
        
        $json = $this->criarJSON($compras, $post['draw'], $qtd_total, $qtd_filtrada);

        return $json;

    }



    /**
     * Recupera as compras permitidas para um usuário, dado uma palavra/frase chave
     * e uma data de início e fim
     * 
     * @param PDO       $dbconn         : Conexão com o BD
     * @param string    $palavra_chave  : Palavra ou frase chave da busca
     * @param string    $data_range     : Intervalo de data
     * @param int       $id_comprador   : ID do Comprador da requisição
     * @param array     $post           : Requisição POST com todo o cabeçalho do DataTables ServerSide
     */
    function recuperarComprasPermitidas($dbconn, $palavra_chave, $data_range, $post) {

        include $_SERVER['DOCUMENT_ROOT'].'/config/sessao.php';
        include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';

        // Separa o intervalo de datas em data de início e fim
        $datas = $data_range;
        if (!empty($datas)) {
            $datas = explode(' - ', $datas);
            $data_inicio = implode('-', array_reverse(explode('/', $datas[0])));
            $data_fim    = implode('-', array_reverse(explode('/', $datas[1])));
        }
        else {
            $data_inicio = '';
            $data_fim    = '';
        }

        /**
         * Variáveis da Sessão
         */
        $username   = $_SESSION['login-username'];
        $email      = $_SESSION['login-email'];


        /**
         * Sem nenhum filtro (todas as compras)
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras AS c
        JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (
            SELECT DISTINCT c.Comprador_ID
            FROM compras AS c
            JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID
            WHERE c.Comprador_ID IN (
                SELECT DISTINCT c.ID AS Comprador_ID
                FROM grupo_usuarios gu
                JOIN usuarios u on gu.Username = u.Usuario
                JOIN compradores c on u.Email = c.Email
                WHERE gu.ID_Grupo IN (
                    SELECT gu.ID_Grupo
                    FROM grupo_usuarios gu
                    JOIN usuarios u on gu.Username = u.Usuario
                    WHERE u.Usuario = '$username'
                )
            ) OR c.Comprador_ID IN (
                SELECT compradores.ID
                FROM usuarios
                JOIN compradores ON usuarios.Email = compradores.Email
                WHERE usuarios.Email = '$email'
            )
        )
        AND c.Observacoes LIKE '%{$palavra_chave}%'";

        // Caso tenha sido selecionado um range de datas
        if (!empty($data_inicio) && !empty($data_fim))
            $sql .= "AND data >= '{$data_inicio}' AND data <= '{$data_fim}'";


        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        
        $qtd_total = count($rs);


        /**
         * COM LIMIT
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras AS c
        JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (
            SELECT DISTINCT c.Comprador_ID
            FROM compras AS c
            JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID
            WHERE c.Comprador_ID IN (
                SELECT DISTINCT c.ID AS Comprador_ID
                FROM grupo_usuarios gu
                JOIN usuarios u on gu.Username = u.Usuario
                JOIN compradores c on u.Email = c.Email
                WHERE gu.ID_Grupo IN (
                    SELECT gu.ID_Grupo
                    FROM grupo_usuarios gu
                    JOIN usuarios u on gu.Username = u.Usuario
                    WHERE u.Usuario = '$username'
                )
            ) OR c.Comprador_ID IN (
                SELECT compradores.ID
                FROM usuarios
                JOIN compradores ON usuarios.Email = compradores.Email
                WHERE usuarios.Email = '$email'
            )
        )
        AND c.Observacoes LIKE '%{$palavra_chave}%'";

        // Caso tenha sido selecionado um range de datas
        if (!empty($data_inicio) && !empty($data_fim))
            $sql .= "AND data >= '{$data_inicio}' AND data <= '{$data_fim}'";
        

        // Preenche a SQL de acordo com as variáveis do DataTable Server Side
        $sql .= $this->preencherSQL($post['search']['value'], $post['order'][0]['column'], $post['order'][0]['dir'], $post['length'], $post['start']);
        

        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $compras = $stmt->fetchAll();


        /**
         * SEM LIMIT (mas com filtros)
         */

        $sql = "SELECT c.*, cmpd.Nome AS Nome_Comprador
        FROM compras AS c
        JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID
        WHERE cmpd.ID IN (
            SELECT DISTINCT c.Comprador_ID
            FROM compras AS c
            JOIN compradores AS cmpd ON c.Comprador_ID = cmpd.ID
            WHERE c.Comprador_ID IN (
                SELECT DISTINCT c.ID AS Comprador_ID
                FROM grupo_usuarios gu
                JOIN usuarios u on gu.Username = u.Usuario
                JOIN compradores c on u.Email = c.Email
                WHERE gu.ID_Grupo IN (
                    SELECT gu.ID_Grupo
                    FROM grupo_usuarios gu
                    JOIN usuarios u on gu.Username = u.Usuario
                    WHERE u.Usuario = '$username'
                )
            ) OR c.Comprador_ID IN (
                SELECT compradores.ID
                FROM usuarios
                JOIN compradores ON usuarios.Email = compradores.Email
                WHERE usuarios.Email = '$email'
            )
        )
        AND c.Observacoes LIKE '%{$palavra_chave}%'";

        // Caso tenha sido selecionado um range de datas
        if (!empty($data_inicio) && !empty($data_fim))
            $sql .= "AND data >= '{$data_inicio}' AND data <= '{$data_fim}'";


        // =======================================================
        // Campo de busca
        // =======================================================

        if (!empty($post['search']['value']))
            $sql .= 'AND c.Observacoes LIKE \'%'.strtoupper($post['search']['value']).'%\' ';



        $stmt = $dbconn->prepare($sql);
        $stmt->execute();

        $rs = $stmt->fetchAll();

        $qtd_filtrada = count($rs);


        
        // =======================================================
        // Cria o JSON
        // =======================================================

        $json = $this->criarJSON($compras, $post['draw'], $qtd_total, $qtd_filtrada);

        return $json;

    }


    // =======================================================
    //                       HELPERS
    // =======================================================


    /**
     * Helper para preencher a SQL à partir das outras informações
     * da requisição DataTable Server Side
     * 
     * @return string   $json: JSON criado
     */
    private function preencherSQL($search_value, $order_column, $order_dir, $limit, $offset) {

        $sql = '';

        // =======================================================
        // Campo de busca
        // =======================================================

        if (!empty($search_value))
            $sql .= 'AND c.Observacoes LIKE \'%'.strtoupper($search_value).'%\' ';

        // =======================================================
        // Ordenação
        // =======================================================

        switch ($order_column) {
            case 0:
                $sql .= 'ORDER BY c.Id '.$order_dir.' ';
                break;
            case 1:
                $sql .= 'ORDER BY c.Data '.$order_dir.' ';
                break;
            case 2:
                $sql .= 'ORDER BY c.Observacoes '.$order_dir.' ';
                break;
            case 3:
                $sql .= 'ORDER BY c.Valor '.$order_dir.' ';
                break;
            case 4:
                $sql .= 'ORDER BY c.Desconto '.$order_dir.' ';
                break;
            case 5:
                $sql .= 'ORDER BY c.Forma_Pagamento '.$order_dir.' ';
                break;
            case 6:
                $sql .= 'ORDER BY c.Comprador_ID '.$order_dir.' ';
                break;
            
            default:
                # code...
                break;
        }

        // =======================================================
        // Limite e Deslocamento
        // =======================================================

        $sql .= 'LIMIT '.$limit.' OFFSET '.$offset;

        return $sql;

    }

    /**
     * Helper para criar o JSON à partir dos dados das compras
     * 
     * @return string   $json: JSON criado
     */
    private function criarJSON($compras, $draw, $qtd_total, $qtd_filtrada) {

        $json = '{
            "draw": '.$draw.',
            "recordsTotal": '.$qtd_total.',
            "recordsFiltered": '.$qtd_filtrada.',
            "data": [';

        $total = count($compras);
        $i = 1;
        foreach ($compras as $compra) {

            // Cria o botão de edição
            $btnEdit = "<button type='button' id-compra='".$compra['Id']."' class='btn-simples'><i class='fas fa-edit'></i></button>";

            $json .= '[
                        "'.$compra['Observacoes'].'",
                        "'.$compra['Data'].'",
                        "'.$compra['Id'].'",
                        "'.$compra['Valor'].'",
                        "'.$compra['Desconto'].'",
                        "'.$compra['Forma_Pagamento'].'",
                        "'.$compra['Nome_Comprador'].'",
                        "'.$btnEdit.'"
                    ]';
            if ($i < $total)
                $json .= ',';
            $i++;
        
        }

        $json .= ']}';

        return $json;

    }
    
    
}
