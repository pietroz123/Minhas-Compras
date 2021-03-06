<?php
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';
    include $_SERVER['DOCUMENT_ROOT'].'/database/dbconnection.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/logica-usuarios.php';

    verifica_usuario();


    include $_SERVER['DOCUMENT_ROOT'].'/config/sessao.php';

    if (isset($_POST['email'])) {

        // Recebe o Email da requisicao POST
        $email_usuario_temp = $_POST['email'];
        if (remover_usuario_temp($dbconn, $email_usuario_temp)) {
            
            $_SESSION['success'] = "Requisição removida com sucesso.";
            header("Location: ../perfil-usuario.php");
            die();
    
        } else {
            
            $_SESSION['danger'] = "Erro na remoção da requisição.";
            header("Location: ../perfil-usuario.php");
            die();
        
        }

    }