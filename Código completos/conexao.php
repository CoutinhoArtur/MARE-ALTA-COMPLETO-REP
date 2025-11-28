<?php
// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "Senai@118"; // Mantenha sua senha
$dbname = "sistema_esportes"; // Nome do banco de dados

// Cria a conexão com o banco de dados MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ========== ADICIONA COLUNA 'imagem' À TABELA 'produtos' (SE NÃO EXISTIR) ==========
$sql = "SHOW COLUMNS FROM produtos LIKE 'imagem'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE produtos ADD COLUMN imagem VARCHAR(255)";
    $conn->query($sql);
}

// ========== ADICIONA COLUNA 'imagem_full' À TABELA 'produtos' (SE NÃO EXISTIR) ==========
// Esta coluna armazenará o caminho da imagem em resolução maior para o frontend
$sql = "SHOW COLUMNS FROM produtos LIKE 'imagem_full'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE produtos ADD COLUMN imagem_full VARCHAR(255)";
    $conn->query($sql);
}

// ========== ADICIONA COLUNA 'imagem' À TABELA 'fornecedores' (SE NÃO EXISTIR) ==========
$sql = "SHOW COLUMNS FROM fornecedores LIKE 'imagem'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE fornecedores ADD COLUMN imagem VARCHAR(255)";
    $conn->query($sql);
}

// ========== ADICIONA COLUNA 'imagem_full' À TABELA 'fornecedores' (SE NÃO EXISTIR) ==========
// Esta coluna armazenará o caminho da imagem em resolução maior para o frontend
$sql = "SHOW COLUMNS FROM fornecedores LIKE 'imagem_full'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE fornecedores ADD COLUMN imagem_full VARCHAR(255)";
    $conn->query($sql);
}
?>