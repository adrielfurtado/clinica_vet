<?php
include 'db_connect.php';

$sql = "SELECT c.data_consulta, c.hora_consulta, c.motivo, 
               a.nome as animal, cl.nome as dono, v.nome as vet
        FROM consultas c
        JOIN animais a ON c.id_animal = a.id_animal
        JOIN clientes cl ON a.id_cliente = cl.id_cliente
        JOIN veterinarios v ON c.id_vet = v.id_vet
        ORDER BY c.data_consulta DESC, c.hora_consulta DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Consultas</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #000; }
        h1 { text-align: center; color: #003366; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #666; font-size: 0.9em; margin-bottom: 30px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th { background-color: #eee; border: 1px solid #000; padding: 8px; text-align: left; }
        td { border: 1px solid #000; padding: 8px; }
        
        @media print {
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

    <h1>Histórico de Consultas</h1>
    <p class="subtitle">Clínica Veterinária Pata & Vida - Gerado em <?php echo date('d/m/Y H:i'); ?></p>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Hora</th>
                <th>Paciente (Dono)</th>
                <th>Veterinário</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($row['data_consulta'])); ?></td>
                        <td><?php echo substr($row['hora_consulta'], 0, 5); ?></td>
                        <td><?php echo htmlspecialchars($row['animal'] . " (" . $row['dono'] . ")"); ?></td>
                        <td><?php echo htmlspecialchars($row['vet']); ?></td>
                        <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center">Nenhum registro encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>