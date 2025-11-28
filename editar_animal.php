<?php
include 'db_connect.php';
$id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_animal'];
    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $raca = $_POST['raca'];
    $idade = $_POST['idade'];
    $sexo = $_POST['sexo'];
    $id_cliente = $_POST['id_cliente'];
    $status = $_POST['status'];
    
    $nome_final = $_POST['foto_atual'];

    if (!empty($_FILES['foto']['name'])) {
        $nome_arquivo = time() . "_" . $_FILES['foto']['name'];
        $destino = "uploads/" . $nome_arquivo;
        
        if (!is_dir("uploads")) mkdir("uploads", 0777, true);

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $nome_final = $nome_arquivo;
        }
    } 
    else if (!empty($_POST['foto_url_texto'])) {
        $nome_final = $_POST['foto_url_texto'];
    }

    $sql = "UPDATE animais SET nome=?, especie=?, raca=?, idade=?, sexo=?, id_cliente=?, foto_url=?, status=? WHERE id_animal=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisissi", $nome, $especie, $raca, $idade, $sexo, $id_cliente, $nome_final, $status, $id);
    
    if ($stmt->execute()) {
        header("Location: admin_animais.php");
        exit;
    }
}

$animal = $conn->query("SELECT * FROM animais WHERE id_animal = $id")->fetch_assoc();
$clientes = $conn->query("SELECT * FROM clientes ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal</title>
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
            <a href="index.php" style="background-color: #004a8f; text-align: center; color: white; font-weight: bold;">Sair</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Editar Animal: <?php echo htmlspecialchars($animal['nome']); ?></h2>
            
            <div style="margin-bottom: 15px; text-align: center;">
                <?php 
                    $img_src = 'assets/img/vet.png';
                    if (!empty($animal['foto_url'])) {
                        if (file_exists("uploads/" . $animal['foto_url'])) {
                            $img_src = "uploads/" . $animal['foto_url'];
                        } else {
                            $img_src = $animal['foto_url'];
                        }
                    }
                ?>
                <img src="<?php echo $img_src; ?>" style="max-width: 150px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
            </div>

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_animal" value="<?php echo $animal['id_animal']; ?>">
                <input type="hidden" name="foto_atual" value="<?php echo $animal['foto_url']; ?>">

                <div class="form-group"><label>Nome:</label><input type="text" name="nome" value="<?php echo htmlspecialchars($animal['nome']); ?>" required></div>

                 <div class="form-group">
                    <label>Dono:</label>
                    <select name="id_cliente">
                        <?php while($c = $clientes->fetch_assoc()): ?>
                            <option value="<?php echo $c['id_cliente']; ?>" <?php echo ($c['id_cliente'] == $animal['id_cliente'])?'selected':''; ?>>
                                <?php echo htmlspecialchars($c['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group"><label>Espécie:</label><input type="text" name="especie" value="<?php echo htmlspecialchars($animal['especie']); ?>"></div>
                <div class="form-group"><label>Raça:</label><input type="text" name="raca" value="<?php echo htmlspecialchars($animal['raca']); ?>"></div>
                <div class="form-group"><label>Idade:</label><input type="number" name="idade" value="<?php echo htmlspecialchars($animal['idade']); ?>"></div>
                
                <div class="form-group">
                    <label>Sexo:</label>
                    <select name="sexo">
                        <option value="I" <?php echo ($animal['sexo'] == 'I') ? 'selected' : ''; ?>>Indefinido</option>
                        <option value="M" <?php echo ($animal['sexo'] == 'M') ? 'selected' : ''; ?>>Macho</option>
                        <option value="F" <?php echo ($animal['sexo'] == 'F') ? 'selected' : ''; ?>>Fêmea</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="Liberado" <?php echo ($animal['status'] == 'Liberado') ? 'selected' : ''; ?>>Liberado</option>
                        <option value="Em Tratamento" <?php echo ($animal['status'] == 'Em Tratamento') ? 'selected' : ''; ?>>Em Tratamento</option>
                        <option value="Internado" <?php echo ($animal['status'] == 'Internado') ? 'selected' : ''; ?>>Internado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Alterar foto:</label>
                    <input type="file" name="foto" accept="image/*">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-submit" style="flex:1;">Salvar Alterações</button>
                    <a href="excluir_animal.php?id=<?php echo $animal['id_animal']; ?>" class="btn btn-delete" style="flex:1; text-align:center; padding-top:10px;" onclick="return confirm('ATENÇÃO: Deseja realmente excluir este animal?');">Excluir Animal</a>
                </div>
            </form>
            <a href="admin_animais.php" class="btn-back">Voltar para a lista</a>
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