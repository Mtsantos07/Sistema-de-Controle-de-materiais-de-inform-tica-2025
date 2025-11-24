reservas.php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Painel de Controle - Sistema de Controle de Materiais</title>
<link rel="stylesheet" href="index.css">
<nav>
  <div class="logo"></div>!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistema de Controle de Materiais de Informática</title>
    <link rel="stylesheet" href="index.css">
     <link rel="stylesheet" href="index.html">
</head>

<body>

    <!-- Cabeçalho -->
    <header>
        <div class="container">
            <h1>Sistema de Controle de Materiais de Informática</h1>
            <nav>
                <ul>
                 
  <div class="logo"></div>
  <a href="equipamento.php">Equipamento</a>
  <a href="painel_estoque.php">estoque</a>
  <a href="associados.html">associados</a>
  <a href="feedback.html">feedback</a>
  <a href="login.php">login</a>
  <a href="criar-conta.php">criar conta</a>
</nav>

<?php
include 'config.php';

// Inicializa variáveis
$erro = '';
$sucesso = '';

// Buscar usuários e equipamentos para os selects
$usuarios = $pdo->query("SELECT id_usuario, nome FROM usuarios ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
// Buscar equipamentos
$equipamentos = $pdo->query("SELECT id, nome FROM equipamentos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

// Verifica se equipamento existe antes do insert
$stmtEquip = $pdo->prepare("SELECT COUNT(*) FROM equipamentos WHERE id = ?");


// Adicionar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Usa operador nulo coalescente para evitar undefined array key
    $id_usuario     = $_POST['id_usuario'] ?? null;
   $id_equipamento = $_POST['equipamento_id'] ?? null;

    $data_reserva   = $_POST['data_reserva'] ?? null;
    $data_uso       = $_POST['data_uso'] ?? null;
    $hora_inicio    = $_POST['hora_inicio'] ?? null;
    $status         = $_POST['status'] ?? 'ativa';

    // Validação básica
    if (!$id_usuario || !$id_equipamento || !$data_reserva || !$data_uso || !$hora_inicio) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        try {
            // Verifica se usuário existe
            $stmtUser = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE id_usuario = ?");
            $stmtUser->execute([$id_usuario]);
            if ($stmtUser->fetchColumn() == 0) {
                throw new Exception("Usuário inválido.");
            }

            // Verifica se equipamento existe
            $stmtEquip = $pdo->prepare("SELECT COUNT(*) FROM equipamentos WHERE id = ?");
            $stmtEquip->execute([$id_equipamento]);
            if ($stmtEquip->fetchColumn() == 0) {
                throw new Exception("Equipamento inválido.");
            }

            // Inserir reserva
            $stmt = $pdo->prepare("
                INSERT INTO reservas
                (id_usuario, id_equipamento, data_reserva, data_uso, hora_inicio, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $id_usuario,
                $id_equipamento,
                $data_reserva,
                $data_uso,
                $hora_inicio,
                $status
            ]);
            $sucesso = "Reserva cadastrada com sucesso!";
        } catch (Exception $e) {
            $erro = "Erro ao cadastrar a reserva: " . $e->getMessage();
        }
    }
}

// Deletar reserva
if (isset($_GET['delete'])) {
    $id_delete = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_delete]);
    header("Location: reservas.php");
    exit;
}

// Buscar reservas para exibir
$reservas = $pdo->query("
    SELECT 
        r.id_reserva,
        u.nome AS usuario_nome,
        e.nome AS equipamento_nome,
        r.data_reserva,
        r.data_uso,
        r.hora_inicio,
        r.status
    FROM reservas r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    JOIN equipamentos e ON r.id_equipamento = e.id
    ORDER BY r.data_reserva DESC, r.hora_inicio ASC
")->fetchAll(PDO::FETCH_ASSOC);
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

<form method="POST">
    <?php if($erro): ?><div class="error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>

    <select name="id_usuario" required>
        <option value="">Selecione o usuário</option>
        <?php foreach($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nome']) ?></option>
        <?php endforeach; ?>
    </select>

   <select name="equipamento_id" required>
    <option value="">Selecione o equipamento</option>
    <?php foreach($equipamentos as $e): ?>
        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
    <?php endforeach; ?>
</select>


    <input type="date" name="data_reserva" required>
    <input type="date" name="data_uso" required>
    <input type="time" name="hora_inicio" required>

    <select name="status">
        <option value="ativa">Ativa</option>
        <option value="cancelada">Cancelada</option>
        <option value="concluida">Concluída</option>
    </select>

    <button type="submit">Reservar Equipamento</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuário</th>
            <th>Equipamento</th>
            <th>Data Reserva</th>
            <th>Data Uso</th>
            <th>Hora Início</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($reservas)): ?>
            <tr><td colspan="8" style="text-align:center;">Nenhuma reserva cadastrada.</td></tr>
        <?php else: ?>
            <?php foreach($reservas as $r): ?>
                <tr>
                    <td><?= $r['id_reserva'] ?></td>
                    <td><?= htmlspecialchars($r['usuario_nome']) ?></td>
                    <td><?= htmlspecialchars($r['equipamento_nome']) ?></td>
                    <td><?= $r['data_reserva'] ?></td>
                    <td><?= $r['data_uso'] ?></td>
                    <td><?= $r['hora_inicio'] ?></td>
                    <td><?= ucfirst($r['status']) ?></td>
                    <td>
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
