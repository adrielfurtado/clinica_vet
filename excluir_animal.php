<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM animais WHERE id_animal = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Animal excluído com sucesso!";
        $_SESSION['msg_type'] = "success";
    } else {
        if ($conn->errno == 1451) {
            $_SESSION['message'] = "Não é possível excluir: Este animal possui histórico de consultas. Apague as consultas primeiro.";
        } else {
            $_SESSION['message'] = "Erro ao excluir: " . $stmt->error;
        }
        $_SESSION['msg_type'] = "error";
    }
    $stmt->close();
}

$conn->close();
header("Location: admin_animais.php");
exit;
?>