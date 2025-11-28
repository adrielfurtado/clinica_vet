<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM consultas WHERE id_consulta = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Consulta excluída com sucesso!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Erro ao excluir consulta: " . $stmt->error;
        $_SESSION['msg_type'] = "error";
    }
    $stmt->close();
}

$conn->close();
header("Location: admin_consultas.php");
exit;
?>