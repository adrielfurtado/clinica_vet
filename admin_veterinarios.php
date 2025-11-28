<?php
include 'db_connect.php';

$search_query = "";
$sql = "SELECT * FROM veterinarios ORDER BY nome";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM veterinarios WHERE nome LIKE ?";
    $stmt = $conn->prepare($sql);
    $term = "%" . $search_query . "%";
    $stmt->bind_param("s", $term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin Veterinários</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title">
            <img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span>
        </h2>
        <nav>
            <a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                Dashboard
            </a>
            <a href="admin_clientes.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_clientes.php', 'novo_cliente.php', 'editar_cliente.php'])) ? 'active' : ''; ?>">
                Clientes
            </a>
            <a href="admin_animais.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_animais.php', 'novo_animal.php', 'editar_animal.php'])) ? 'active' : ''; ?>">
                Animais
            </a>
            <a href="admin_veterinarios.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_veterinarios.php', 'novo_veterinario.php', 'editar_veterinario.php'])) ? 'active' : ''; ?>">
                Veterinários
            </a>
            <a href="admin_consultas.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_consultas.php', 'nova_consulta.php'])) ? 'active' : ''; ?>">
                Consultas
            </a>
            <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">
            <a href="index.php" style="background-color: #004a8f; text-align: center; color: white; font-weight: bold;">
                Sair
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h1>Veterinários</h1>

            <?php
                if (isset($_SESSION['message'])) {
                    $tipo = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'success';
                    echo "<div class='message $tipo'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                    }
                ?>

            <div class="toolbar">
                <form method="GET">
                    <input type="text" name="search" placeholder="Buscar veterinário..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit">Buscar</button>
                </form>
                <a href="novo_veterinario.php" class="btn btn-create">Novo Veterinário</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th><th>Nome</th><th>CRMV</th><th>Especialidade</th><th>Telefone</th><th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_vet']; ?></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo htmlspecialchars($row['crmv']); ?></td>
                            <td><?php echo htmlspecialchars($row['especialidade']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefone']); ?></td>
                            <td>
                                <a href="editar_veterinario.php?id=<?php echo $row['id_vet']; ?>" class="btn btn-edit">Editar</a>
                                <a href="excluir_veterinario.php?id=<?php echo $row['id_vet']; ?>" class="btn btn-delete" onclick="return confirm('Deseja excluir este veterinário?');">Excluir</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Nenhum registro encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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