<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pata & Vida - Início</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>

        body {
            flex-direction: column; 
            padding: 0;
            margin: 0;
            overflow-x: hidden;
        }
        .main-header {
            width: 100%;
            box-sizing: border-box;
        }
        .hero-section {
            flex: 1; 
            width: 100%;
            box-sizing: border-box;
        }
        .main-footer {
            width: 100%;
            margin-top: 0;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="header-brand">
            <img src="assets/img/vet.png" alt="Logo Pata & Vida">
            <span>Pata & Vida</span>
        </div>
        
        <nav>
            <a href="#" style="color: white; text-decoration: none; margin-left: 20px;">Sobre</a>
        </nav>
    </header>
    <div class="hero-section">
        <h1>Gerenciamento de Dados</h1>
        <p>Bem-vindo ao sistema administrativo da Clínica Pata & Vida.</p>
        
        <a href="dashboard.php" class="btn-enter">
            Acessar Painel do Sistema
        </a>
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

</body>
</html>