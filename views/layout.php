<!doctype html>
<html lang="pt-br"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>ERP PHP</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
<style>
  body { background-color: #f8f9fa; }
  .page-content { animation: fadeInUp 0.4s ease; }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translate3d(0, 16px, 0); }
    to { opacity: 1; transform: translate3d(0, 0, 0); }
  }
</style>
</head><body class="bg-light">
<nav class="navbar navbar-expand-lg bg-white border-bottom mb-3"><div class="container">
  <a class="navbar-brand" href="/">ERP PHP</a>
  <div class="navbar-nav">
    <a class="nav-link" href="/contracts">Contratos</a>
    <a class="nav-link" href="/invoices">Faturas</a>
    <a class="nav-link" href="/reports/costs">Relatórios</a>
  </div>
</div></nav>
<div class="container page-content">{{content}}</div>
</body></html>
