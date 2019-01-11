<?php
    include("database/conexao.php");
?>

<?php

    // Inicia a sessao
    if (!isset($_SESSION) || !is_array($_SESSION)) {
        session_start();
    }

    // Verifica se o botão de submit foi pressionado
    if (!isset($_POST['submit-recuperacao'])) {
        $_SESSION['danger'] = "Você não deu submit!";
        header("Location: index.php");
    }
    else {

        // Configuracoes do servidor
        $config = parse_ini_file('../private/config_compras.ini');
        $site = $config['site'];
        $email_servidor = $config['email_servidor'];


        // Seletor e token para seguranca
        $seletor = bin2hex(random_bytes(8));
        $token = random_bytes(32);

        // Url que o usuario devera acessar para criar uma nova senha
        $url = $site . "/criar-nova-senha.php?seletor=" . $seletor . "&token=" . bin2hex($token);

        // Tempo de expiracao de uma hora
        $expira = date("U") + 1800;


        // Recebe o e-mail enviado pelo formulario
        $email_usuario = $_POST['email'];


        // Deleta todos os possiveis tokens ainda existentes e relacionados ao e-mail do usuario
        $query = "DELETE FROM recuperacao_senha WHERE email = ?;";
        $stmt = mysqli_stmt_init($conexao);
        if (!mysqli_stmt_prepare($stmt, $query)) {
            $_SESSION['danger'] = "Ocorreu um erro ao deletar os tokens pre-existentes.";
            header("Location: recuperacao-senha.php");
            die();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $email_usuario);
            mysqli_stmt_execute($stmt);
        }


        // Insere o novo token no banco de dados
        $query = "INSERT INTO recuperacao_senha (email, seletor, token, expira) VALUES (?, ?, ?, ?);";
        if (!mysqli_stmt_prepare($stmt, $query)) {
            $_SESSION['danger'] = "Ocorreu um erro na inserção da requisição da senha.";
            header("Location: recuperacao-senha.php");
            die();
        } else {
            $hash_token = password_hash($token, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ssss", $email_usuario, $seletor, $hash_token, $expira);
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_stmt_close($stmt);



        // Envia o e-mail
        $para = $email_usuario;
        $assunto = "Recupere sua senha para a Loja do Pietro";
        
        $mensagem = "<!DOCTYPE html>";
        $mensagem .= "<html>";
        $mensagem .= "<head>";
        $mensagem .= "<meta charset='utf-8' />";
        $mensagem .= "<meta name='viewport' content='width=device-width, initial-scale=1'>";
        $mensagem .= "</head>";
        $mensagem .= "<body style='background-color: #f2f2f2;'>";
        $mensagem .= "<h1 style='position: fixed;left: 0;top: 0;width: 99.5%;text-align: center;font-size: 20px;background-color: #990000;padding: 25px 0;margin: 0;color: white;text-transform: uppercase;border: 3px solid black;'>Informações para a recuperação da sua senha</h1>";
        $mensagem .= "<div class='texto-email' style='margin: 120px 30px;text-align: justify;font-size: 18px;'>";
        $mensagem .= "<p style='margin-bottom: 15px;'>Recebemos uma requisição de recuperação de senha. O link para recuperar sua senha está logo abaixo. Se você não fez essa requisição, ignore este e-email.</p>";
        $mensagem .= "<p style='margin-bottom: 15px;'>Aqui está o link de recuperação da senha:<br><a href='" . $url ."'>" . $url ."</a></p>";
        $mensagem .= "</div>";
        $mensagem .= "<footer style='position: fixed;left: 0;bottom: 0;width: 100%;'>";
        $mensagem .= "<p style='font-size: 18px;background-color: #990000;padding: 25px;margin: 0;color: white;border: 3px solid black;'>Pietro Zuntini Bonfim &copy;</p>";
        $mensagem .= "</footer>";
        $mensagem .= "</body>";
        $mensagem .= "</html>";

        $headers = "From: Pietro <" . $email_servidor . "\r\n";
        $headers .= "Reply-To: " . $email_servidor . "\r\n";
        $headers .= "Content-type: text/html\r\n";

        mail($para, $assunto, $mensagem, $headers);


        // Retorna para a pagina inicial
        $_SESSION['success'] = "Um e-mail foi enviado para '" . $email_usuario . "' com as informações de recuperação de senha. Verifique seu e-mail.";
        header("Location: recuperacao-senha.php");

    }