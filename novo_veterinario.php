<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $crmv = $_POST['crmv'];
    $especialidade = $_POST['especialidade'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    if (empty($nome) || empty($crmv) || empty($especialidade) || empty($telefone) || empty($email)) {
        $erro = "Erro: Todos os campos são obrigatórios!";
    } else {
        $stmt = $conn->prepare("INSERT INTO veterinarios (nome, crmv, especialidade, telefone, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $crmv, $especialidade, $telefone, $email);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Veterinário cadastrado com sucesso!";
            header("Location: admin_veterinarios.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Veterinário</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title"><img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span></h2>
        <nav>
            <a href="admin_clientes.php">Clientes</a>
            <a href="admin_animais.php">Animais</a>
            <a href="admin_veterinarios.php" class="active">Veterinários</a>
            <a href="admin_consultas.php">Consultas</a>
        </nav>
    </div>
    <div class="main-content">
        <div class="container">
            <h2>Novo Veterinário</h2>
            <?php if(isset($erro)) echo "<div class='message message-error'>$erro</div>"; ?>
            
            <form method="POST">
                <div class="form-group"><label>Nome:</label><input type="text" name="nome" required></div>
                <div class="form-group"><label>CRMV:</label><input type="text" name="crmv" required></div>
                <div class="form-group"><label>Especialidade:</label><input type="text" name="especialidade" required></div>
                <div class="form-group"><label>Telefone:</label><input type="tel" id="telefone" name="telefone" maxlength="15" required></div>
                <div class="form-group"><label>Email:</label><input type="email" name="email" required></div>
                <button type="submit" class="btn btn-submit">Salvar</button>
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