<?php
include 'db_connect.php';

$message = '';
$message_type = '';
$nome = ''; $cpf = ''; $telefone = ''; $email = ''; $cep = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];

    if (empty($nome)) {
        $message = "O campo 'Nome' é obrigatório.";
        $message_type = 'message-error';
    } else {

        $check_sql = "SELECT id_cliente FROM clientes WHERE cpf = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $cpf, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Erro: Já existe um cliente cadastrado com este CPF ou E-mail.";
            $message_type = 'message-error';
            $check_stmt->close();
        } else {
            $check_stmt->close();

            $sql = "INSERT INTO clientes (nome, cpf, telefone, email, cep) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $nome, $cpf, $telefone, $email, $cep);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Cliente cadastrado com sucesso!";
                header("Location: admin_clientes.php");
                exit;
            } else {
                $message = "Erro ao cadastrar: " . $stmt->error;
                $message_type = 'message-error';
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Cliente</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title"><img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span></h2>
        <nav>
            <a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Início (Dashboard)</a>
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
            <h2>Adicionar Novo Cliente</h2>

            <?php if (!empty($message)) { echo "<div class='message $message_type'>$message</div>"; } ?>

            <form action="novo_cliente.php" method="POST">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                </div>
                <div class="form-group">
                    <label>CPF:</label>
                    <input type="text" id="cpf" name="cpf" maxlength="14" value="<?php echo htmlspecialchars($cpf); ?>" required>
                </div>
                <div class="form-group">
                    <label>Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" maxlength="15" value="<?php echo htmlspecialchars($telefone); ?>">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group">
                    <label>CEP:</label>
                    <input type="text" id="cep" name="cep" maxlength="9" value="<?php echo htmlspecialchars($cep); ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-submit">Salvar Cliente</button>
                </div>
            </form>
            <a href="admin_clientes.php" class="btn-back">Voltar para a lista</a>
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