<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Cliente excluído com sucesso!";
        $_SESSION['msg_type'] = "success";
    } else {
        if ($conn->errno == 1451) {
            $_SESSION['message'] = "Não é possível excluir: Este cliente possui animais cadastrados. Exclua os animais primeiro.";
        } else {
            $_SESSION['message'] = "Erro ao excluir: " . $stmt->error;
        }
        $_SESSION['msg_type'] = "error";
    }
    $stmt->close();
}

$conn->close();
header("Location: admin_clientes.php");
exit;
?>