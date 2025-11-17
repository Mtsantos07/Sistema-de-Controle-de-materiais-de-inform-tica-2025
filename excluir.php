excluir.php
<?php
include 'config.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) die("ID inválido.");

$id = (int) $id;

// Confirma existência
$stmt = $pdo->prepare("SELECT * FROM equipamentos WHERE id=?");
$stmt->execute([$id]);
if (!$stmt->fetch()) die("Equipamento não encontrado.");

// Exclui
$delete = $pdo->prepare("DELETE FROM equipamentos WHERE id=?");
$delete->execute([$id]);

header("Location: index.php");
exit;
