<?php
    include $_SERVER['DOCUMENT_ROOT'].'/database/conexao.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/funcoes-usuarios.php';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/logica-usuarios.php';
?>

<?php

    if (!isset($_POST['submit'])) {
        $_SESSION['danger'] = "Você não deu submit!";
        header("Location: ../index.php");
        die();
    }

    // Pega os dados da requisicao POST
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $username = $_POST['usuario'];
    $email_cadastro = $_POST['email'];
    $senha_cadastro = $_POST['senha'];
    $senha_rep_cadastro = $_POST['senha-rep'];

    // Verifica se existem campos em branco
    if (empty($nome) || empty($sobrenome) || empty($username) || empty($email_cadastro) || empty($senha_cadastro) || empty($senha_rep_cadastro)) {
        $_SESSION['danger'] = "Existem campos em branco!";
        header("Location: ../index.php");
        die();
    }

    // Verifica se a repeticao de senha e igual
    if ($senha_cadastro != $senha_rep_cadastro) {
        $_SESSION['danger'] = "As senhas não são iguais!";
        header("Location: ../index.php");
        die();
    }

    // Cria o usuario
    if (criar_usuario($conexao, $nome, $sobrenome, $username, $email_cadastro, $senha_cadastro)) {
        $_SESSION['success'] = "Cadastrado com sucesso. Favor esperar a confirmação do cadastro.";
        header("Location: ../index.php");
        die();
    } else {
        $_SESSION['danger'] = "Erro ao cadastrar.";
        header("Location: ../index.php");
        die();
    }