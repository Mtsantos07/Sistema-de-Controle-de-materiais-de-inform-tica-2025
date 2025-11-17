reservas.php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Painel de Controle - Sistema de Controle de Materiais</title>
<link rel="stylesheet" href="index.css">
<nav>
  <div class="logo"></div>
  <a href="home.html">home</a>
  <a href="painel_estoque.php">estoque</a>
  <a href="reservas.php">reservas</a>
  <a href="feedback.html">feedback</a>
  <a href="login.php">login</a>
  <a href="criar-conta.php">criar conta</a>
</nav>


  <?php
include 'config.php';

// Mensagens
$erro = '';
$sucesso = '';

// Adicionar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipamento_id = $_POST['equipamento_id'];
    $usuario_nome = trim($_POST['usuario_nome']);
    $data_reserva = $_POST['data_reserva'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $status = $_POST['status'];

    if (!$equipamento_id || !$usuario_nome || !$data_reserva || !$hora_inicio || !$hora_fim) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO reservas (user_id, equipamento_id, data_reserva) VALUES (?, ?, ?)
 VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$equipamento_id, $usuario_nome, $data_reserva, $hora_inicio, $hora_fim, $status])) {
            $sucesso = "Reserva cadastrada com sucesso!";
        } else {
            $erro = "Erro ao cadastrar a reserva.";
        }
    }
}

// Deletar reserva
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$user_id, $equipamento_id, $data_reserva]);
;
    header("Location: reservas.php");
    exit;
}

// Buscar reservas
$reservas = $pdo->query("
   SELECT r.*, e.nome as equipamento_nome 
FROM reservas r 
JOIN equipamentos e ON r.id_equipamento = e.id
ORDER BY r.data_reserva DESC, r.hora_inicio ASC

")->fetchAll(PDO::FETCH_ASSOC);

// Buscar equipamentos para o select
$equipamentos = $pdo->query("SELECT * FROM equipamentos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>CRUD de Reservas</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; padding:20px; }
h1 { text-align:center; color:#333; }
form { max-width:500px; margin:auto; display:flex; flex-direction:column; gap:10px; background:#fff; padding:20px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
input, select { padding:10px; border-radius:5px; border:1px solid #ccc; width:100%; }
button { padding:10px; border:none; border-radius:5px; background:#4CAF50; color:#fff; font-weight:bold; cursor:pointer; }
button:hover { background:#45a049; }
.error { color:red; text-align:center; }
.success { color:green; text-align:center; }
table { width:100%; border-collapse: collapse; margin-top:30px; background:#fff; }
th, td { padding:10px; border:1px solid #ccc; text-align:left; }
th { background:#4CAF50; color:#fff; }
a.btn { padding:5px 10px; text-decoration:none; border-radius:5px; margin-right:5px; color:#fff; }
a.edit { background:#ff9800; }
a.delete { background:#f44336; }
</style>
</head>
<body>

<h1>CRUD de Reservas de Equipamentos</h1>

<!-- Formulário de cadastro -->
<form method="POST">
    <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <select name="equipamento_id" required>
        <option value="">Selecione o equipamento</option>
        <?php foreach($equipamentos as $e): ?>
            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
        <?php endforeach; ?>
    </select>

    <input type="text" name="usuario_nome" placeholder="Nome do usuário" required>
    <input type="date" name="data_reserva" required>
    <input type="time" name="hora_inicio" required>
    <input type="time" name="hora_fim" required>
    <select name="status">
        <option value="pendente">Pendente</option>
        <option value="confirmada">Confirmada</option>
        <option value="cancelada">Cancelada</option>
    </select>

    <button type="submit">Reservar Equipamento</button>
</form>

<!-- Tabela de reservas -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Equipamento</th>
            <th>Usuário</th>
            <th>Data</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($reservas) === 0): ?>
            <tr><td colspan="8" style="text-align:center;">Nenhuma reserva cadastrada.</td></tr>
        <?php else: ?>
            <?php foreach($reservas as $r): ?>
                <tr>
                    <td><?= $r['id_reserva'] ?></td>
                    <td><?= htmlspecialchars($r['equipamento_nome']) ?></td>
                    <td><?= htmlspecialchars($r['usuario_nome']) ?></td>
                    <td><?= $r['data_reserva'] ?></td>
                    <td><?= $r['hora_inicio'] ?></td>
                    <td><?= $r['hora_fim'] ?></td>
                    <td><?= ucfirst($r['status']) ?></td>
                    <td>
                        <!-- Futuro: editar reserva -->
                        <a href="editar-reserva.php?id=<?= $r['id_reserva'] ?>" class="btn edit">Editar</a>
                        <a href="?delete=<?= $r['id_reserva'] ?>" class="btn delete" onclick="return confirm('Confirma exclusão?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
