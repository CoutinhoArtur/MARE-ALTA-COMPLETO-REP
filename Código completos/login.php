<?php
// Inicia uma sessão para armazenar informações do usuário durante a navegação.
session_start();

// Inclui o arquivo de conexão com o banco de dados.
include('conexao.php');

// Verifica se a requisição foi feita através do método POST (envio do formulário).
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados enviados pelo formulário (usuário e senha).
    $usuario = $_POST['usuario'];
    // Aplica o algoritmo MD5 para criptografar a senha antes de verificar no banco de dados.
    $senha = md5($_POST['senha']);

    // Monta a consulta SQL para verificar se o usuário e senha existem no banco.
    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND senha='$senha'";
    // Executa a consulta e armazena o resultado.
    $result = $conn->query($sql);

    // Verifica se a consulta retornou algum registro.
    if ($result->num_rows > 0) {
        // Se o usuário for encontrado, armazena seu nome na sessão.
        $_SESSION['usuario'] = $usuario;
        // Redireciona o usuário para a página inicial do painel (agora painel.php).
        header('Location: painel.php');
    } else {
        // Se o login falhar, define uma mensagem de erro.
        $error = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel SportShop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="width: 400px; margin-top: 50px;">
        <h2>Login do Painel</h2>
        <form method="post" action="">
            <label for="usuario">Usuário:</label>
            <input type="text" name="usuario" required>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit" style="margin-bottom: 30px;" >Entrar</button>
            <!-- Exibe a mensagem de erro, se houver. -->
            <?php if (isset($error)) echo "<p class='message error'>$error</p>"; ?>
        </form>
        <!-- Link de voltar agora aponta para index.php (a nova home) -->
        <a href="index.php" class="back-button" style="background-color: #7f8c8d;">Voltar para o Site</a>
    </div>
</body>
</html>