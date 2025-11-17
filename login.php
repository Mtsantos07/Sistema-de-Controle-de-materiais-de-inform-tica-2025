login.php
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Painel de Controle - Sistema de Controle de Materiais</title>
<link rel="stylesheet" href="index.css">
<nav>
  <div class="logo"></div>
  <a href="equipamento.php">equipamentos</a>
  <a href="painel_estoque.php">estoque</a>
  <a href="reservas.php">reservas</a>
  <a href="feedback.html">feedback</a>
  <a href="login.php">login</a>
  <a href="criar-conta.php">criar conta</a>
</nav>

  <!-- Login -->
  <section id="login" class="container" style="margin-top: 40px;">
    <div class="section-title">Login</div>
    <form>
      <label for="usuario">Nome de usuário:</label>
      <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário" required />
      <label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required />
      <button type="submit">Entrar</button>
    </form>
  </section>
