<?php
include 'db_connect.php';

$search_query = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT c.*, a.nome AS nome_animal, v.nome AS nome_vet 
            FROM consultas c
            LEFT JOIN animais a ON c.id_animal = a.id_animal
            LEFT JOIN veterinarios v ON c.id_vet = v.id_vet
            WHERE a.nome LIKE ? OR v.nome LIKE ? OR c.motivo LIKE ?
            ORDER BY c.data_consulta DESC, c.hora_consulta DESC";
            
    $stmt = $conn->prepare($sql);
    $term = "%" . $search_query . "%";
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    $sql = "SELECT c.*, a.nome AS nome_animal, v.nome AS nome_vet 
            FROM consultas c
            LEFT JOIN animais a ON c.id_animal = a.id_animal
            LEFT JOIN veterinarios v ON c.id_vet = v.id_vet
            ORDER BY c.data_consulta DESC, c.hora_consulta DESC";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title> Admin Consultas</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title"><img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span></h2>
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
            
            <h1>Agenda de Consultas</h1>

            <?php
                if (isset($_SESSION['message'])) {
                    $tipo = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'success';
                    echo "<div class='message $tipo'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                    }
                ?>

            <div class="toolbar">
                
                <form action="admin_consultas.php" method="GET" style="display: flex; margin: 0;">
                    <input type="text" name="search" placeholder="Buscar..." value="<?php echo htmlspecialchars($search_query); ?>" style="border-radius: 4px 0 0 4px; border-right: none;">
                    <button type="submit" style="border-radius: 0 4px 4px 0;">Buscar</button>
                </form>

                <div style="display: flex; gap: 10px;">
                    <a href="relatorios.php" target="_blank" class="btn btn-create" style="background-color: #0056b3;">
                        Baixar PDF
                    </a>

                    <a href="nova_consulta.php" class="btn btn-create">
                        Nova Consulta
                    </a>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Animal</th>
                        <th>Veterinário</th>
                        <th>Motivo</th>
                        <th style="width: 120px;">Ações</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['data_consulta'])); ?></td>
                                <td><?php echo substr($row['hora_consulta'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($row['nome_animal']); ?></td>
                                <td><?php echo htmlspecialchars($row['nome_vet']); ?></td>
                                <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                
                                <td>
                                    <a href="editar_consulta.php?id=<?php echo $row['id_consulta']; ?>" class="btn btn-edit" style="margin-bottom: 5px;">
                                        Editar
                                    </a>
                                   <a href="excluir_consulta.php?id=<?php echo $row['id_consulta']; ?>" 
                                    class="btn btn-delete" 
                                    onclick="return confirm('Tem certeza?');">
                                     Excluir
                                   </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Nenhuma consulta encontrada.</td></tr>
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