<?php
include 'db_connect.php';
$id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id_vet'];
    $stmt = $conn->prepare("UPDATE veterinarios SET nome=?, crmv=?, especialidade=?, telefone=?, email=? WHERE id_vet=?");
    $stmt->bind_param("sssssi", $_POST['nome'], $_POST['crmv'], $_POST['especialidade'], $_POST['telefone'], $_POST['email'], $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Veterinário atualizado!";
        header("Location: admin_veterinarios.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM veterinarios WHERE id_vet = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$vet = $stmt->get_result()->fetch_assoc();
if(!$vet) header("Location: admin_veterinarios.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Veterinário</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title"><img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span></h2>
        <nav>
            <a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
            <a href="admin_clientes.php">Clientes</a>
            <a href="admin_animais.php">Animais</a>
            <a href="admin_veterinarios.php" class="active">Veterinários</a>
            <a href="admin_consultas.php">Consultas</a>
            <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">
            <a href="index.php" style="background-color: #004a8f; text-align: center; color: white; font-weight: bold;">Sair</a>
        </nav>
    </div>
    <div class="main-content">
        <div class="container">
            <h2>Editar: <?php echo htmlspecialchars($vet['nome']); ?></h2>
            <form method="POST">
                <input type="hidden" name="id_vet" value="<?php echo $vet['id_vet']; ?>">
                <div class="form-group"><label>Nome:</label><input type="text" name="nome" value="<?php echo $vet['nome']; ?>" required></div>
                <div class="form-group"><label>CRMV:</label><input type="text" name="crmv" value="<?php echo $vet['crmv']; ?>"></div>
                <div class="form-group"><label>Especialidade:</label><input type="text" name="especialidade" value="<?php echo $vet['especialidade']; ?>"></div>
                <div class="form-group"><label>Telefone:</label><input type="tel" id="telefone" name="telefone" value="<?php echo $vet['telefone']; ?>" maxlength="15"></div>
                <div class="form-group"><label>Email:</label><input type="email" name="email" value="<?php echo $vet['email']; ?>"></div>
                <button type="submit" class="btn btn-submit">Atualizar</button>
            </form>
            <a href="admin_veterinarios.php" class="btn-back">Voltar</a>
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
    <script src="assets/js/masks.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>