<?php
include 'db_connect.php';

$sql = "SELECT a.*, c.nome as nome_cliente 
        FROM animais a 
        LEFT JOIN clientes c ON a.id_cliente = c.id_cliente 
        ORDER BY a.nome";
        
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin Animais</title>
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
            
            <div class="toolbar">
                <h1>Animais</h1>
                <?php
                if (isset($_SESSION['message'])) {
                    $tipo = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'success';
                    echo "<div class='message $tipo'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['msg_type']);
                }
                ?>
                <a href="novo_animal.php" class="btn btn-create">Novo Animal</a>
            </div>

            <div class="animal-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($animal = $result->fetch_assoc()): ?>
                        
                        <a href="editar_animal.php?id=<?php echo $animal['id_animal']; ?>" class="animal-card" title="Clique para editar">
                            
                            <?php
                            $img_path = $animal['foto_url'];
                            $foto_final = 'assets/img/vet.png';

                            if (!empty($img_path)) {
                                if (file_exists("uploads/" . $img_path)) {
                                    $foto_final = "uploads/" . $img_path;
                                } else {
                                    $foto_final = $img_path;
                                }
                            }
                            
                            $status_class = strtolower(str_replace(' ', '-', $animal['status']));
                            ?>

                            <div class="animal-card-image">
                                <img src="<?php echo htmlspecialchars($foto_final); ?>" alt="Foto">
                            </div>
                            
                            <div class="animal-card-info">
                                <h3><?php echo htmlspecialchars($animal['nome']); ?></h3>
                                <p><strong>Dono:</strong> <?php echo htmlspecialchars($animal['nome_cliente'] ?? 'Sem dono'); ?></p>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo $animal['status']; ?>
                                </span>
                            </div>
                        </a>
                        
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Nenhum animal cadastrado.</p>
                <?php endif; ?>
            </div> 

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
<?php $conn->close(); ?>