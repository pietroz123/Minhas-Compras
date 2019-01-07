<?php
    include("conexao.php");
    include("funcoes-usuarios.php");
    include("logica-usuarios.php");
?>

<?php

    $email_usuario = $_POST['email'];
    $senha_usuario = $_POST['senha'];

    $usuario = buscar_usuario($conexao, $email_usuario, $senha_usuario);
    
    if ($usuario == null) {
        $_SESSION['danger'] = "Usuario ou senha invalido.";
        header("Location: index.php");
    } else {
        login($email_usuario);
        $_SESSION['success'] = "Logado com sucesso.";
        header("Location: index.php");
    }
    die();