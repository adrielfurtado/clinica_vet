<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: admin_consultas.php");
    exit;
}
$id_consulta = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM consultas WHERE id_consulta = ?");
$stmt->bind_param("i", $id_consulta);
$stmt->execute();
$consulta = $stmt->get_result()->fetch_assoc();

if (!$consulta) {
    header("Location: admin_consultas.php");
    exit;
}

$sql_itens = "SELECT cs.*, s.nome_servico 
              FROM consulta_servicos cs 
              JOIN servicos s ON cs.id_servico = s.id_servico 
              WHERE cs.id_consulta = ?";
$stmt_itens = $conn->prepare($sql_itens);
$stmt_itens->bind_param("i", $id_consulta);
$stmt_itens->execute();
$result_itens = $stmt_itens->get_result();

$itens_existentes = [];
while ($row = $result_itens->fetch_assoc()) {
    $itens_existentes[] = $row;
}

$json_itens = json_encode($itens_existentes);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_animal = $_POST['id_animal'];
    $id_vet = $_POST['id_vet'];
    $data = $_POST['data_consulta'];
    $hora = $_POST['hora_consulta'];
    $motivo = $_POST['motivo'];
    $obs = $_POST['observacoes'];
    
    $servicos_ids = $_POST['servicos'] ?? [];
    $quantidades = $_POST['quantidades'] ?? [];
    $valores = $_POST['valores'] ?? [];

    $conn->begin_transaction();

    try {

        $stmt_update = $conn->prepare("UPDATE consultas SET data_consulta=?, hora_consulta=?, motivo=?, observacoes=?, id_animal=?, id_vet=? WHERE id_consulta=?");
        $stmt_update->bind_param("ssssiii", $data, $hora, $motivo, $obs, $id_animal, $id_vet, $id_consulta);
        $stmt_update->execute();

        $stmt_del = $conn->prepare("DELETE FROM consulta_servicos WHERE id_consulta = ?");
        $stmt_del->bind_param("i", $id_consulta);
        $stmt_del->execute();

        $stmt_add = $conn->prepare("INSERT INTO consulta_servicos (id_consulta, id_servico, quantidade, valor_cobrado) VALUES (?, ?, ?, ?)");
        
        for ($i = 0; $i < count($servicos_ids); $i++) {
            $stmt_add->bind_param("iiid", $id_consulta, $servicos_ids[$i], $quantidades[$i], $valores[$i]);
            $stmt_add->execute();
        }

        $conn->commit();
        $_SESSION['message'] = "Consulta atualizada com sucesso!";
        header("Location: admin_consultas.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "Erro: " . $e->getMessage();
    }
}

$animais = $conn->query("SELECT id_animal, nome FROM animais ORDER BY nome");
$veterinarios = $conn->query("SELECT id_vet, nome FROM veterinarios ORDER BY nome");
$servicos = $conn->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome_servico");

$servicos_array = [];
while($s = $servicos->fetch_assoc()) { $servicos_array[] = $s; }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Consulta</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .box-servicos { background: #f0f8ff; padding: 15px; border-radius: 8px; border: 1px solid #cce5ff; margin-bottom: 20px; }
        .total-geral { font-size: 1.4em; font-weight: bold; text-align: right; margin-top: 15px; color: #003366; }
        .btn-remove { background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2 class="sidebar-title"><img src="assets/img/vet.png" class="sidebar-logo"><span>Pata & Vida</span></h2>
        <nav>
            <a href="dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>
            <a href="admin_clientes.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_clientes.php', 'novo_cliente.php', 'editar_cliente.php'])) ? 'active' : ''; ?>">Clientes</a>
            <a href="admin_animais.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_animais.php', 'novo_animal.php', 'editar_animal.php'])) ? 'active' : ''; ?>">Animais</a>
            <a href="admin_veterinarios.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_veterinarios.php', 'novo_veterinario.php', 'editar_veterinario.php'])) ? 'active' : ''; ?>">Veterinários</a>
            <a href="admin_consultas.php" class="<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['admin_consultas.php', 'nova_consulta.php', 'editar_consulta.php'])) ? 'active' : ''; ?>">Consultas</a>
            <hr style="border: 0; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">
            <a href="index.php" style="background-color: #004a8f; text-align: center; color: white; font-weight: bold;">Sair</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Editar Consulta</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label>Animal:</label>
                    <select name="id_animal" required>
                        <?php while($a = $animais->fetch_assoc()): ?>
                            <option value="<?php echo $a['id_animal']; ?>" <?php echo ($a['id_animal'] == $consulta['id_animal']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($a['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Veterinário:</label>
                    <select name="id_vet" required>
                        <?php while($v = $veterinarios->fetch_assoc()): ?>
                            <option value="<?php echo $v['id_vet']; ?>" <?php echo ($v['id_vet'] == $consulta['id_vet']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($v['nome']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Data:</label>
                        <input type="date" name="data_consulta" required value="<?php echo $consulta['data_consulta']; ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Hora:</label>
                        <input type="time" name="hora_consulta" required value="<?php echo substr($consulta['hora_consulta'], 0, 5); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Motivo:</label>
                    <input type="text" name="motivo" required value="<?php echo htmlspecialchars($consulta['motivo']); ?>">
                </div>

                <h3>Serviços e Procedimentos</h3>
                <div class="box-servicos">
                    <div style="display:flex; gap:10px; align-items: flex-end;">
                        <div style="flex:1;">
                            <label>Adicionar Serviço:</label>
                            <select id="selectServico">
                                <option value="">Escolha um serviço...</option>
                                <?php foreach($servicos_array as $srv): ?>
                                    <option value="<?php echo $srv['id_servico']; ?>" data-preco="<?php echo $srv['valor_padrao']; ?>">
                                        <?php echo $srv['nome_servico']; ?> (R$ <?php echo number_format($srv['valor_padrao'], 2, ',', '.'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="btn btn-create" onclick="addServicoManual()">Adicionar</button>
                    </div>

                    <table id="tabelaServicos" style="margin-top:15px; background:white;">
                        <thead><tr><th>Serviço</th><th>Qtd</th><th>Valor Un.</th><th>Subtotal</th><th></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <div class="total-geral">Total: R$ <span id="spanTotal">0,00</span></div>
                </div>

                <div class="form-group">
                    <label>Observações:</label>
                    <textarea name="observacoes" rows="3"><?php echo htmlspecialchars($consulta['observacoes']); ?></textarea>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
    
                    <button type="submit" class="btn btn-submit" style="flex:1;">
                        Salvar Alterações
                    </button>
    
                    <a href="admin_consultas.php" class="btn btn-delete" style="flex:1; text-align:center; padding: 10px 15px; text-decoration: none; display: inline-block;">
                        Cancelar
                    </a>

                </div>
            </form>
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

    <script>
        function criarLinha(id, nome, qtd, valor) {
            const tbody = document.querySelector('#tabelaServicos tbody');
            const row = document.createElement('tr');
            const subtotal = (qtd * valor).toFixed(2);

            row.innerHTML = `
                <td>
                    ${nome}
                    <input type="hidden" name="servicos[]" value="${id}">
                </td>
                <td>
                    <input type="number" name="quantidades[]" value="${qtd}" min="1" style="width:60px" onchange="calc(this)">
                </td>
                <td>
                    <input type="number" name="valores[]" value="${valor}" step="0.01" style="width:80px" onchange="calc(this)">
                </td>
                <td class="subtotal">R$ ${subtotal}</td>
                <td><button type="button" class="btn-remove" onclick="removeRow(this)">X</button></td>
            `;
            tbody.appendChild(row);
            calcTotal();
        }

        function addServicoManual() {
            const select = document.getElementById('selectServico');
            const id = select.value;
            if(!id) return;

            const nome = select.options[select.selectedIndex].text.split(' (R$')[0];
            const preco = parseFloat(select.options[select.selectedIndex].getAttribute('data-preco'));

            criarLinha(id, nome, 1, preco);
            select.value = "";
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            calcTotal();
        }

        function calc(input) {
            const row = input.closest('tr');
            const qtd = row.querySelector('input[name="quantidades[]"]').value;
            const val = row.querySelector('input[name="valores[]"]').value;
            row.querySelector('.subtotal').innerText = "R$ " + (qtd * val).toFixed(2);
            calcTotal();
        }

        function calcTotal() {
            let total = 0;
            document.querySelectorAll('#tabelaServicos tbody tr').forEach(row => {
                const qtd = row.querySelector('input[name="quantidades[]"]').value;
                const val = row.querySelector('input[name="valores[]"]').value;
                total += (qtd * val);
            });
            document.getElementById('spanTotal').innerText = total.toFixed(2).replace('.', ',');
        }
        const itensSalvos = <?php echo $json_itens; ?>;
        
        window.onload = function() {
            itensSalvos.forEach(item => {
                criarLinha(item.id_servico, item.nome_servico, item.quantidade, item.valor_cobrado);
            });
        };
    </script>
    <script src="assets/js/theme.js"></script>
</body>
</html>
<?php $conn->close(); ?>