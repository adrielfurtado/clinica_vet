<?php
include 'db_connect.php';

$cliente = null;
$message = ''; 
$message_type = '';
$id = $_GET['id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_cliente = $_POST['id_cliente'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];

    $check_sql = "SELECT id_cliente FROM clientes WHERE (cpf = ? OR email = ?) AND id_cliente != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssi", $cpf, $email, $id_cliente);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "Erro: Já existe OUTRO cliente usando este CPF ou E-mail.";
        $message_type = 'message-error';
        $check_stmt->close();

        $cliente = [
            'id_cliente' => $id_cliente, 'nome' => $nome, 'cpf' => $cpf, 
            'telefone' => $telefone, 'email' => $email, 'cep' => $cep
        ];

    } else {
        $check_stmt->close();
        $sql = "UPDATE clientes SET nome=?, cpf=?, telefone=?, email=?, cep=? WHERE id_cliente=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $cpf, $telefone, $email, $cep, $id_cliente);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Cliente atualizado com sucesso!";
            header("Location: admin_clientes.php");
            exit;
        } else {
            $message = "Erro ao atualizar: " . $stmt->error;
            $message_type = 'message-error';
        }
        $stmt->close();
    }
}

if ($id && empty($cliente)) {
    $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $cliente = $result->fetch_assoc();
    } else {
        $_SESSION['message'] = "Cliente não encontrado.";
        header("Location: admin_clientes.php");
        exit;
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Cliente</title>
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
            <h2>Editar Cliente: <?php echo htmlspecialchars($cliente['nome']); ?></h2>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="editar_cliente.php" method="POST">
                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
                
                <div class="form-group"><label>Nome:</label><input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required></div>
                <div class="form-group"><label>CPF:</label><input type="text" id="cpf" name="cpf" maxlength="14" value="<?php echo htmlspecialchars($cliente['cpf']); ?>" required></div>
                <div class="form-group"><label>Telefone:</label><input type="tel" id="telefone" name="telefone" maxlength="15" value="<?php echo htmlspecialchars($cliente['telefone']); ?>"></div>
                <div class="form-group"><label>Email:</label><input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>"></div>
                <div class="form-group"><label>CEP:</label><input type="text" id="cep" name="cep" maxlength="9" value="<?php echo htmlspecialchars($cliente['cep']); ?>" required></div>

                <div class="form-group">
                    <button type="submit" class="btn btn-submit">Atualizar Cliente</button>
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