<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'];
    $idade = $_POST['idade'];
    $sexo = $_POST['sexo'];
    $id_cliente = $_POST['id_cliente'];
    $status = $_POST['status'];
    
    $nome_final = 'assets/img/vet.png';

    if (empty($nome) || empty($especie) || empty($raca) || empty($idade) || empty($sexo) || empty($id_cliente) || empty($status)) {
        $message = "Erro: Preencha todos os dados do animal (Nome, Espécie, Raça, Idade, Sexo, Dono e Status).";
        $message_type = 'message-error';
    } else {
        
        if (!empty($_FILES['foto']['name'])) {
            $nome_arquivo = time() . "_" . $_FILES['foto']['name'];
            $destino = "uploads/" . $nome_arquivo;
            if (!is_dir("uploads")) mkdir("uploads", 0777, true);
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
                $nome_final = $nome_arquivo;
            }
        } elseif (!empty($_POST['foto_url_texto'])) {
            $nome_final = $_POST['foto_url_texto'];
        }

        $sql = "INSERT INTO animais (nome, especie, raca, idade, sexo, id_cliente, foto_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisiss", $nome, $especie, $raca, $idade, $sexo, $id_cliente, $nome_final, $status);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Animal cadastrado com sucesso!";
            header("Location: admin_animais.php");
            exit;
        } else {
            $message = "Erro: " . $stmt->error;
            $message_type = 'message-error';
        }
    }
}

$clientes = $conn->query("SELECT id_cliente, nome FROM clientes ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Animal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title"><img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span></h2>
        <nav>
            <a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
            <a href="admin_clientes.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_clientes.php', 'novo_cliente.php', 'editar_cliente.php'])) ? 'active' : ''; ?>">Clientes</a>
            <a href="admin_animais.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_animais.php', 'novo_animal.php', 'editar_animal.php'])) ? 'active' : ''; ?>">Animais</a>
            <a href="admin_veterinarios.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_veterinarios.php', 'novo_veterinario.php', 'editar_veterinario.php'])) ? 'active' : ''; ?>">Veterinários</a>
            <a href="admin_consultas.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_consultas.php', 'nova_consulta.php'])) ? 'active' : ''; ?>">Consultas</a>
            <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">
            <a href="index.php" style="background-color: #004a8f; border: 1px solid #005bb3; text-align: center; color: white; font-weight: normal;">Sair</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Novo Animal</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group"><label>Nome:</label><input type="text" name="nome" required></div>
                
                <div class="form-group">
                    <label>Dono:</label>
                    <select name="id_cliente" required>
                        <option value="">Selecione...</option>
                        <?php while($c = $clientes->fetch_assoc()): ?>
                            <option value="<?php echo $c['id_cliente']; ?>"><?php echo $c['nome']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group"><label>Espécie:</label><input type="text" name="especie"></div>
                <div class="form-group"><label>Raça:</label><input type="text" name="raca"></div>
                <div class="form-group"><label>Idade:</label><input type="number" name="idade"></div>
                
                <div class="form-group">
                    <label>Sexo:</label>
                    <select name="sexo">
                        <option value="I">Indefinido</option><option value="M">Macho</option><option value="F">Fêmea</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="Liberado">Liberado</option><option value="Em Tratamento">Em Tratamento</option><option value="Internado">Internado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Foto do Animal (Upload):</label>
                    <input type="file" name="foto" accept="image/*">
                </div>

                <button type="submit" class="btn btn-submit">Salvar</button>
            </form>
            <a href="admin_animais.php" class="btn-back">Voltar</a>
        </div>
        <footer class="main-footer">
            <div class="footer-content">   
                <div class="copyright-area">
                    <p>&copy; 2025 Pata & Vida - Desenvolvido por <strong>Adriel</strong></p>
                    <div class="social-links">
                    <a href="https://www.instagram.com/aadriel_furtado/" target="_blank">Instagram</a>
                    <a href="https://www.linkedin.com/in/adriel-furtado-08b555385/" target="_blank">LinkedIn</a>
                    <a href="https://github.com/d3kinho" target="_blank">GitHub</a>
                    </div>
                </div>
                <button id="theme-toggle-btn" title="Alternar Tema">
                    <img src="assets/img/dark-mode.png" alt="Modo Escuro">
                </button>
            </div>
        </footer>
    </div>
    <script src="assets/js/theme.js"></script>
</body>
</html>