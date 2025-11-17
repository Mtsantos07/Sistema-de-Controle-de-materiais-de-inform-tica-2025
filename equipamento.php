equipamento.php
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Sistema de Controle de Materiais de Informática - Home</title>

    <link rel="stylesheet" href="index.css" />

</head>

<body>
    <header>
        <div class="container">
            <h1>Sistema de Controle de Materiais de Informática</h1>
            <nav>
                <a href="equipamento.php">home</a>
                <a href="painel_estoque.php">estoque</a>
                <a href="associados.html">associados</a>
                <a href="feedback.html">feedback</a>
                <a href="login.php">login</a>
                <a href="criar-conta.php">criar conta</a>
            </nav>
        </div>
    </header>

   
<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>CRUD Equipamentos</title>
<style>
body { font-family: Arial; margin: 20px; background-color:#f4f4f4;}
table { width:100%; border-collapse: collapse; background:#fff; color:#000000ff; }
th, td { padding:8px; border:1px solid #000000ff; text-align:left; }
th { background:#4CAF50; color:#000000ff; }
a.btn { padding:5px 10px; text-decoration:none; color:#000000ff; border-radius:4px; margin:2px; }
a.add { background:#4CAF50; }
a.edit { background:#ff9800; }
a.delete { background:#f44336; }
</style>
</head>
<body>
<h1>Sistema de Controle de Equipamentos</h1>
<a href="adicionar.php" class="btn add">Adicionar Novo</a>

<table>
<thead>
<tr>
<th>id</th><th>Nome</th><th>Tipo</th><th>Status</th><th>Localização</th><th>Data Cadastro</th><th>Ações</th>
</tr>
</thead>
<tbody>
<?php
$stmt = $pdo->query("SELECT * FROM equipamentos ORDER BY id DESC");
if ($stmt->rowCount() == 0) {
    echo "<tr><td colspan='7' style='text-align:center;'>Nenhum equipamento cadastrado.</td></tr>";
} else {
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($row['id'])."</td>";
        echo "<td>".htmlspecialchars($row['nome'])."</td>";
        echo "<td>".htmlspecialchars($row['tipo'])."</td>";
        echo "<td>".htmlspecialchars($row['status'])."</td>";
        echo "<td>".htmlspecialchars($row['localizacao'])."</td>";
        echo "<td>".htmlspecialchars($row['data_cadastro'])."</td>";
        echo "<td>
            <a href='editar.php?id=".urlencode($row['id'])."' class='btn edit'>Editar</a>
            <a href='excluir.php?id=".urlencode($row['id'])."' class='btn delete' onclick='return confirm(\"Confirma exclusão?\")'>Excluir</a>
        </td>";
        echo "</tr>";
    }
}
?>
</tbody>
</table>
</body>
</html>

    <footer>
        &copy; 2025 Sistema de Controle de Materiais de Informática
    </footer>
</body>

</html>
