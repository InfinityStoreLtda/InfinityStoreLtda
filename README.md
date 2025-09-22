# ERP PHP

Aplicação PHP simples seguindo padrão MVC leve para gestão de contratos, faturas e relatórios.

## Requisitos

- PHP 8.1+
- Extensões: PDO (MySQL), mbstring, intl
- Composer

## Instalação

```bash
composer install
cp config.php config.local.php # ajuste credenciais se necessário
```

A aplicação carrega automaticamente `config.local.php`, quando presente, mesclando-o ao `config.php`. Ajuste suas credenciais nesse arquivo local (ou diretamente em `config.php`, se preferir) e aponte o servidor web para o diretório `public/`.

## Cron

Para gerar faturas automaticamente no primeiro dia do mês, agende o script `cron/faturar_mensal.php` no crontab.
