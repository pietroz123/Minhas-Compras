<?php

function buscar_usuario($conexao, $email) {
    $query = "SELECT * FROM usuarios WHERE email = '{$email}';";
    $resultado = mysqli_query($conexao, $query);
    $usuario = mysqli_fetch_assoc($resultado);
    return $usuario;
}

function criar_usuario($conexao, $nome_usuario, $email, $senha) {
    $hash_senha = password_hash($senha, PASSWORD_DEFAULT);
    $query = "INSERT INTO usuarios (usuario, email, senha) VALUES ('$nome_usuario', '$email', '$hash_senha');";
    $resultado = mysqli_query($conexao, $query);
    return $resultado;
}