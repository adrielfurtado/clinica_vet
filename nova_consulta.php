<?php
include 'db_connect.php';

$animais = $conn->query("SELECT id_animal, nome FROM animais ORDER BY nome");
$veterinarios = $conn->query("SELECT id_vet, nome FROM veterinarios ORDER BY nome");
$servicos = $conn->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome_servico");

$servicos_array = [];
while($s = $servicos->fetch_assoc()) { $servicos_array[] = $s; }

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
        $stmt = $conn->prepare("INSERT INTO consultas (data_consulta, hora_consulta, motivo, observacoes, id_animal, id_vet) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $data, $hora, $motivo, $obs, $id_animal, $id_vet);
        $stmt->execute();
        $id_consulta = $conn->insert_id;

        $stmt_item = $conn->prepare("INSERT INTO consulta_servicos (id_consulta, id_servico, quantidade, valor_cobrado) VALUES (?, ?, ?, ?)");
        
        for ($i = 0; $i < count($servicos_ids); $i++) {
            $stmt_item->bind_param("iiid", $id_consulta, $servicos_ids[$i], $quantidades[$i], $valores[$i]);
            $stmt_item->execute();
        }

        $conn->commit();
        $_SESSION['message'] = "Consulta registrada com sucesso!";
        header("Location: admin_consultas.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "Erro: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Nova Consulta</title>
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
            <a href="admin_clientes.php">Clientes</a>
            <a href="admin_animais.php">Animais</a>
            <a href="admin_veterinarios.php">Veterinários</a>
            <a href="admin_consultas.php" class="active">Consultas</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container">
            <h2>Nova Consulta</h2>
            <form method="POST">
                <div class="form-group"><label>Animal:</label>
                    <select name="id_animal" required>
                        <option value="">Selecione...</option>
                        <?php while($a = $animais->fetch_assoc()): ?>
                            <option value="<?php echo $a['id_animal']; ?>"><?php echo htmlspecialchars($a['nome']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group"><label>Veterinário:</label>
                    <select name="id_vet" required>
                        <option value="">Selecione...</option>
                        <?php while($v = $veterinarios->fetch_assoc()): ?>
                            <option value="<?php echo $v['id_vet']; ?>"><?php echo htmlspecialchars($v['nome']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;"><label>Data:</label><input type="date" name="data_consulta" required value="<?php echo date('Y-m-d'); ?>"></div>
                    <div class="form-group" style="flex:1;"><label>Hora:</label><input type="time" name="hora_consulta" required value="<?php echo date('H:i'); ?>"></div>
                </div>
                <div class="form-group"><label>Motivo:</label><input type="text" name="motivo" required></div>

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
                        <button type="button" class="btn btn-create" onclick="addServico()">Adicionar</button>
                    </div>

                    <table id="tabelaServicos" style="margin-top:15px; background:white;">
                        <thead><tr><th>Serviço</th><th>Qtd</th><th>Valor Un.</th><th>Subtotal</th><th></th></tr></thead>
                        <tbody></tbody>
                    </table>
                    <div class="total-geral">Total: R$ <span id="spanTotal">0,00</span></div>
                </div>

                <div class="form-group"><label>Observações:</label><textarea name="observacoes" rows="3" style="width:100%"></textarea></div>
                <button type="submit" class="btn btn-submit">Salvar Consulta</button>
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
        <script src="assets/js/theme.js"></script>
    </div>

    <script>
        function addServico() {
            const select = document.getElementById('selectServico');
            const id = select.value;
            if(!id) return;

            const nome = select.options[select.selectedIndex].text.split(' (R$')[0];
            const preco = parseFloat(select.options[select.selectedIndex].getAttribute('data-preco'));

            const tbody = document.querySelector('#tabelaServicos tbody');
            const row = document.createElement('tr');
            
            row.innerHTML = `
                <td>${nome}<input type="hidden" name="servicos[]" value="${id}"></td>
                <td><input type="number" name="quantidades[]" value="1" min="1" style="width:60px" onchange="calc(this)"></td>
                <td><input type="number" name="valores[]" value="${preco.toFixed(2)}" step="0.01" style="width:80px" onchange="calc(this)"></td>
                <td class="subtotal">R$ ${preco.toFixed(2)}</td>
                <td><button type="button" class="btn-remove" onclick="removeRow(this)">X</button></td>
            `;
            tbody.appendChild(row);
            calcTotal();
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
    </script>
</body>
</html>