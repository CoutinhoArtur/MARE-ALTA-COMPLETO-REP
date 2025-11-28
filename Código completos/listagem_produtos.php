<?php 
// Inclui o script para validar a sessão do usuário
include('valida_sessao.php'); 

// Inclui o script de conexão com o banco de dados
include('conexao.php'); 
?>

<?php
// Verifica se foi passado um ID para exclusão via GET.
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Busca as imagens do produto antes de excluir para removê-las do servidor
    $img_stmt = $conn->prepare("SELECT imagem, imagem_full FROM produtos WHERE id=?");
    $img_stmt->bind_param("i", $delete_id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result()->fetch_assoc();
    $img_stmt->close();
    
    // Remove os arquivos de imagem do servidor se existirem
    if($img_result['imagem'] && file_exists($img_result['imagem'])) {
        unlink($img_result['imagem']);
    }
    if($img_result['imagem_full'] && file_exists($img_result['imagem_full'])) {
        unlink($img_result['imagem_full']);
    }
    
    // Cria a query SQL para deletar o artigo com o ID correspondente.
    $stmt = $conn->prepare("DELETE FROM produtos WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    
    // Executa a query e define a mensagem de sucesso ou erro.
    if ($stmt->execute()) {
        $mensagem = "Artigo Esportivo excluído com sucesso!";
        $class = "success";
    } else {
        $mensagem = "Erro ao excluir artigo: " . $conn->error;
        $class = "error";
    }
    $stmt->close();
}

// Consulta SQL para listar todos os artigos, incluindo o nome da marca.
$artigos = $conn->query("
    SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, 
           f.nome AS marca_nome 
    FROM produtos p 
    JOIN fornecedores f ON p.fornecedor_id = f.id
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Artigos Esportivos</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Link para o arquivo de estilização CSS -->
</head>
<body>
    <div class="container">
        <h2>Listagem de Artigos Esportivos</h2>

        <!-- Exibe a mensagem de feedback (sucesso ou erro) após uma ação -->
        <?php 
        if (isset($mensagem)) {
            echo "<p class='message " . $class . "'>$mensagem</p>"; 
        } 
        ?>

        <!-- Tabela de exibição dos artigos cadastrados -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Marca</th>
                    <th>Imagem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop para exibir cada artigo retornado da consulta -->
                <?php while ($row = $artigos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nome']; ?></td>
                    <td><?php echo $row['descricao']; ?></td>
                    <td><?php echo 'R$ ' . number_format($row['preco'], 2, ',', '.'); ?></td>
                    <td><?php echo $row['marca_nome']; ?></td>
                    <td>
                        <!-- Mostra a miniatura (thumbnail) da imagem se ela existir -->
                        <?php if ($row['imagem']): ?>
                            <img src="<?php echo $row['imagem']; ?>" alt="Imagem do artigo" class="thumbnail">
                        <?php else: ?>
                            Sem imagem
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Links para editar ou excluir o artigo -->
                        <a href="cadastro_produto.php?edit_id=<?php echo $row['id']; ?>">Editar</a>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Botão para voltar à página principal (painel.php) -->
        <a href="painel.php" class="back-button">Voltar ao Painel</a>
    </div>
</body>
</html>