<?php
// Inclui o arquivo que valida a sessão do usuário
include('valida_sessao.php');
// Inclui o arquivo de conexão com o banco de dados
include('conexao.php');

/**
 * Função para redimensionar e salvar imagem em dois tamanhos
 * @param array $arquivo - Arquivo enviado via $_FILES
 * @param int $largura_thumb - Largura da miniatura (padrão: 80px)
 * @param int $altura_thumb - Altura da miniatura (padrão: 80px)
 * @param int $largura_full - Largura da imagem completa (padrão: 800px)
 * @param int $altura_full - Altura da imagem completa (padrão: 800px)
 * @return array - Array com os caminhos das imagens ou mensagem de erro
 */
function redimensionarESalvarImagemDupla($arquivo, $largura_thumb = 80, $altura_thumb = 80, $largura_full = 800, $altura_full = 800) {
    // Define o diretório de destino para as imagens
    $diretorio_destino = "img/";
    
    // Cria o diretório se ele não existir
    if (!file_exists($diretorio_destino)) {
        mkdir($diretorio_destino, 0777, true);
    }
    
    // Gera um nome único para o arquivo
    $nome_base = uniqid() . '_' . basename($arquivo["name"]);
    $tipo_arquivo = strtolower(pathinfo($nome_base, PATHINFO_EXTENSION));

    // Validações do arquivo
    $check = getimagesize($arquivo["tmp_name"]);
    if($check === false) { 
        return ["erro" => "O arquivo não é uma imagem válida."]; 
    }
    
    if ($arquivo["size"] > 5000000) { 
        return ["erro" => "O arquivo é muito grande. O tamanho máximo permitido é 5MB."]; 
    }
    
    if(!in_array($tipo_arquivo, ["jpg", "jpeg", "png", "gif"])) { 
        return ["erro" => "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos."]; 
    }

    // Carrega a imagem original baseado no tipo
    switch($tipo_arquivo) {
        case "jpg":
        case "jpeg":
            $imagem_original = imagecreatefromjpeg($arquivo["tmp_name"]);
            break;
        case "png":
            $imagem_original = imagecreatefrompng($arquivo["tmp_name"]);
            break;
        case "gif":
            $imagem_original = imagecreatefromgif($arquivo["tmp_name"]);
            break;
    }

    // Obtém dimensões originais
    $largura_original = imagesx($imagem_original);
    $altura_original = imagesy($imagem_original);

    // ========== CRIA THUMBNAIL (miniatura para backend) ==========
    $ratio_thumb = min($largura_thumb / $largura_original, $altura_thumb / $altura_original);
    $nova_largura_thumb = $largura_original * $ratio_thumb;
    $nova_altura_thumb = $altura_original * $ratio_thumb;
    
    $imagem_thumb = imagecreatetruecolor($nova_largura_thumb, $nova_altura_thumb);
    
    // Preserva transparência para PNG e GIF
    if($tipo_arquivo == "png" || $tipo_arquivo == "gif") {
        imagealphablending($imagem_thumb, false);
        imagesavealpha($imagem_thumb, true);
    }
    
    imagecopyresampled($imagem_thumb, $imagem_original, 0, 0, 0, 0, 
                      $nova_largura_thumb, $nova_altura_thumb, 
                      $largura_original, $altura_original);

    // Define nome e caminho da thumbnail
    $nome_thumb = 'thumb_' . $nome_base;
    $caminho_thumb = $diretorio_destino . $nome_thumb;

    // ========== CRIA IMAGEM FULL (maior para frontend) ==========
    $ratio_full = min($largura_full / $largura_original, $altura_full / $altura_original);
    $nova_largura_full = $largura_original * $ratio_full;
    $nova_altura_full = $altura_original * $ratio_full;
    
    $imagem_full = imagecreatetruecolor($nova_largura_full, $nova_altura_full);
    
    // Preserva transparência para PNG e GIF
    if($tipo_arquivo == "png" || $tipo_arquivo == "gif") {
        imagealphablending($imagem_full, false);
        imagesavealpha($imagem_full, true);
    }
    
    imagecopyresampled($imagem_full, $imagem_original, 0, 0, 0, 0, 
                      $nova_largura_full, $nova_altura_full, 
                      $largura_original, $altura_original);

    // Define nome e caminho da imagem full
    $nome_full = 'full_' . $nome_base;
    $caminho_full = $diretorio_destino . $nome_full;

    // Salva ambas as imagens no servidor
    switch($tipo_arquivo) {
        case "jpg":
        case "jpeg":
            imagejpeg($imagem_thumb, $caminho_thumb, 90);
            imagejpeg($imagem_full, $caminho_full, 90);
            break;
        case "png":
            imagepng($imagem_thumb, $caminho_thumb);
            imagepng($imagem_full, $caminho_full);
            break;
        case "gif":
            imagegif($imagem_thumb, $caminho_thumb);
            imagegif($imagem_full, $caminho_full);
            break;
    }

    // Libera a memória
    imagedestroy($imagem_original);
    imagedestroy($imagem_thumb);
    imagedestroy($imagem_full);
    
    // Retorna os caminhos de ambas as imagens
    return [
        "thumb" => $caminho_thumb,
        "full" => $caminho_full
    ];
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $fornecedor_id = $_POST['fornecedor_id']; // ID da marca
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = str_replace(',', '.', $_POST['preco']); // Converte vírgula para ponto

    // Processa o upload da imagem
    $imagem_thumb = "";
    $imagem_full = "";
    
    if(isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $resultado_upload = redimensionarESalvarImagemDupla($_FILES['imagem']);
        
        // Verifica se houve erro no upload
        if(isset($resultado_upload['erro'])) {
            $mensagem_erro = $resultado_upload['erro'];
        } else {
            // Armazena os caminhos das duas versões da imagem
            $imagem_thumb = $resultado_upload['thumb'];
            $imagem_full = $resultado_upload['full'];
        }
    }

    // Prepara a query SQL para inserção ou atualização
    if ($id) {
        // Se o ID existe, é uma atualização
        $sql = "UPDATE produtos SET fornecedor_id=?, nome=?, descricao=?, preco=?";
        $params = [$fornecedor_id, $nome, $descricao, $preco];
        $types = "isss"; // Tipos de dados (integer, string, string, string)
        
        // Se uma nova imagem foi enviada, adiciona à query
        if($imagem_thumb && $imagem_full) {
            $sql .= ", imagem=?, imagem_full=?";
            $params[] = $imagem_thumb;
            $params[] = $imagem_full;
            $types .= "ss"; // Adiciona dois tipos string
        }
        
        $sql .= " WHERE id=?";
        $params[] = $id;
        $types .= "i"; // Adiciona tipo integer para o ID

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $mensagem = "Artigo Esportivo atualizado com sucesso!";
    } else {
        // Se não há ID, é uma nova inserção
        $sql = "INSERT INTO produtos (fornecedor_id, nome, descricao, preco, imagem, imagem_full) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $fornecedor_id, $nome, $descricao, $preco, $imagem_thumb, $imagem_full);
        $mensagem = "Artigo Esportivo cadastrado com sucesso!";
    }

    // Executa a query e verifica se houve erro
    if ($stmt->execute()) {
        $class = "success";
    } else {
        $mensagem = "Erro: " . $stmt->error;
        $class = "error";
    }
    $stmt->close();
}

// Verifica se foi solicitada a exclusão de um artigo
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
    
    // Prossegue com a exclusão no banco de dados
    $sql = "DELETE FROM produtos WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $mensagem = "Artigo Esportivo excluído com sucesso!";
        $class = "success";
    } else {
        $mensagem = "Erro ao excluir artigo: " . $stmt->error;
        $class = "error";
    }
    $stmt->close();
}

// Busca todos os artigos para listar na tabela (com o nome da marca)
$artigos = $conn->query("SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, f.nome AS marca_nome FROM produtos p JOIN fornecedores f ON p.fornecedor_id = f.id");

// Se foi solicitada a edição de um artigo, busca os dados dele
$artigo = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $artigo = $result->fetch_assoc();
    $stmt->close();
}

// Busca todas as marcas para o select do formulário
$marcas = $conn->query("SELECT id, nome FROM fornecedores");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Artigo Esportivo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Cadastro de Artigo Esportivo</h2>
        <!-- Formulário para cadastro/edição de artigo -->
        <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $artigo['id'] ?? ''; ?>">
            
            <label for="fornecedor_id">Marca:</label>
            <select name="fornecedor_id" required>
                <option value="">Selecione uma marca</option>
                <?php while ($row = $marcas->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>" <?php if ($artigo && $artigo['fornecedor_id'] == $row['id']) echo 'selected'; ?>><?php echo $row['nome']; ?></option>
                <?php endwhile; ?>
            </select>
            
            <label for="nome">Nome do Artigo:</label>
            <input type="text" name="nome" value="<?php echo $artigo['nome'] ?? ''; ?>" required>
            
            <label for="descricao">Descrição:</label>
            <textarea name="descricao"><?php echo $artigo['descricao'] ?? ''; ?></textarea>
            
            <label for="preco">Preço:</label>
            <input type="text" name="preco" value="<?php echo $artigo['preco'] ?? ''; ?>" required placeholder="Ex: 99,90">
            
            <label for="imagem">Imagem:</label>
            <input type="file" name="imagem" accept="image/*">
            
            <!-- Mostra a imagem atual (thumbnail) se estiver editando -->
            <?php if (isset($artigo['imagem']) && $artigo['imagem']): ?>
                <p style="margin-top: 10px; color: #666;">Imagem atual:</p>
                <img src="<?php echo $artigo['imagem']; ?>" alt="Imagem atual do artigo" class="update-image">
            <?php endif; ?>
            <br>
            <button type="submit"><?php echo $artigo ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>
        
        <!-- Exibe mensagens de sucesso ou erro -->
        <?php if (isset($mensagem)): ?>
            <p class="message <?php echo $class; ?>"><?php echo $mensagem; ?></p>
        <?php endif; ?>
        <?php if (isset($mensagem_erro)): ?>
            <p class="message error"><?php echo $mensagem_erro; ?></p>
        <?php endif; ?>

        <h2>Listagem de Artigos Esportivos</h2>
        <!-- Tabela para listar os artigos cadastrados -->
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
                <?php $artigos->data_seek(0); // Reinicia o ponteiro do resultado ?>
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
                        <a href="?edit_id=<?php echo $row['id']; ?>">Editar</a>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Link de voltar aponta para painel.php -->
        <a href="painel.php" class="back-button">Voltar ao Painel</a>
    </div>
</body>
</html>