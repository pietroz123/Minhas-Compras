<?php

include $_SERVER['DOCUMENT_ROOT'].'/config/sessao.php';

// Mostra um alerta ao usuario, tanto de sucesso quanto de fracasso
function mostra_alerta($tipo) {
    if (isset($_SESSION[$tipo])) {
?>
        <div class="alert alert-<?= $tipo ?>" style="text-align: center;" role="alert">
            <?= $_SESSION[$tipo] ?>
        </div>
<?php
    unset($_SESSION[$tipo]);
    }
}

// Wrapper com todos os alertas
function mostra_alertas() {
    mostra_alerta('info');
    mostra_alerta('success');
    mostra_alerta('danger');
}

// Verifica se o usuario esta logado e, caso contrario, o redireciona para a pagina principal
function verifica_usuario() {
    if (!usuario_esta_logado()) {
        $_SESSION['danger'] = "Você não tem acesso a essa funcionalidade. Nível de acesso: USUÁRIO LOGADO.";
        header("Location: ../index.php");
        // echo("<script>location.href = '../index.php'</script>");
        die();
    }
}

// Verifica se o usuario esta logado
function usuario_esta_logado() {
    return isset($_SESSION['login']);
}

// Retorna o usuario logado (seu id unico na sessao)
function usuario_logado() {
    return $_SESSION['login-username'];
}

// Efetua o login do usuario
function login($email_usuario, $username, $nome, $id_comprador) {
    if ($username == "admin") {
        $_SESSION['admin'] = true;
    }
    $_SESSION['login'] = true;
    $_SESSION['login-email'] = $email_usuario;
    $_SESSION['login-username'] = $username;
    $_SESSION['login-nome'] = $nome;
    $_SESSION['login-id-comprador'] = $id_comprador;
}

// Efetua o logout do usuario
function logout() {
    session_destroy();
    session_start();
    $_SESSION['success'] = "Deslogado com sucesso.";
    header("Location: ../index.php");
}

// Retorna se o usuario atual e o admin
function admin() {
    return isset($_SESSION['admin']);
}

// Verifica se o usuario esta logado e, caso contrario, o redireciona para a pagina principal
function verifica_admin() {
    if (!admin()) {
        $_SESSION['danger'] = "Você não tem acesso a essa funcionalidade. Nível de acesso: ADMIN.";
        header("Location: ../index.php");
        // echo("<script>location.href = '../index.php'</script>");
        die();
    }
}