<?php
include 'db_connect.php';

$total_clientes = $conn->query("SELECT COUNT(*) FROM clientes")->fetch_row()[0];
$total_animais  = $conn->query("SELECT COUNT(*) FROM animais")->fetch_row()[0];
$total_consultas = $conn->query("SELECT COUNT(*) FROM consultas")->fetch_row()[0];

$sql_grafico = "SELECT DATE_FORMAT(data_consulta, '%m/%Y') as mes, COUNT(*) as total 
                FROM consultas 
                GROUP BY YEAR(data_consulta), MONTH(data_consulta) 
                ORDER BY data_consulta ASC 
                LIMIT 6";
$result_grafico = $conn->query($sql_grafico);

$labels = [];
$data   = [];

while($row = $result_grafico->fetch_assoc()) {
    $labels[] = $row['mes'];
    $data[] = $row['total'];
}

$json_labels = json_encode($labels);
$json_data = json_encode($data);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    
    <div class="sidebar">
        <h2 class="sidebar-title">
            <img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span>
        </h2>
        <nav>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="admin_clientes.php">Clientes</a>
            <a href="admin_animais.php">Animais</a>
            <a href="admin_veterinarios.php">Veterinários</a>
            <a href="admin_consultas.php">Consultas</a>
            <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">
            <a href="index.php" style="background-color: #004a8f; text-align: center; color: white; font-weight: bold;">
                Sair
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h1>Visão Geral da Clínica</h1>

            <div class="dashboard-cards">
                <div class="card">
                    <h3><?php echo $total_clientes; ?></h3>
                    <p>Clientes Cadastrados</p>
                </div>
                <div class="card">
                    <h3><?php echo $total_animais; ?></h3>
                    <p>Animais Atendidos</p>
                </div>
                <div class="card">
                    <h3><?php echo $total_consultas; ?></h3>
                    <p>Consultas Realizadas</p>
                </div>
            </div>

            <div class="chart-container">
                <h2>Estatísticas de Consultas</h2>
                <canvas id="graficoConsultas" height="100"></canvas>
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
        <script src="assets/js/theme.js"></script>
    </div>

    <script>
        const ctx = document.getElementById('graficoConsultas').getContext('2d');
        const isDark = document.body.classList.contains('dark-mode');
        const textColor = isDark ? '#e0e0e0' : '#333';
        const gridColor = isDark ? '#444' : '#ddd';
        const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $json_labels; ?>, 
            datasets: [{
                label: 'Quantidade de Consultas',
                data: <?php echo $json_data; ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: { color: textColor }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, stepSize: 1 },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { display: false }
                }
            }
        }
    });

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === "class") {
                const isDarkNow = document.body.classList.contains('dark-mode');
                
                const newTextColor = isDarkNow ? '#e0e0e0' : '#333';
                const newGridColor = isDarkNow ? '#444' : '#ddd';

                myChart.options.plugins.legend.labels.color = newTextColor;
                myChart.options.scales.y.ticks.color = newTextColor;
                myChart.options.scales.x.ticks.color = newTextColor;
                myChart.options.scales.y.grid.color = newGridColor;
                myChart.update();
            }
        });
    });
    observer.observe(document.body, { attributes: true });
    </script>
</body>
</html>