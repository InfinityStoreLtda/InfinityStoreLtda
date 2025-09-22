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

Configure as credenciais de banco em `config.php` (ou arquivo local carregado manualmente) e aponte o servidor web para o diretório `public/`.

## Cron

Para gerar faturas automaticamente no primeiro dia do mês, agende o script `cron/faturar_mensal.php` no crontab.

## Testes

Os testes automatizados utilizam PHPUnit com banco SQLite em memória para simular os contratos e faturas. Após instalar as dependências de desenvolvimento com o Composer, execute:

```bash
composer install
vendor/bin/phpunit
```

O arquivo `phpunit.xml` na raiz já aponta para a suíte localizada em `tests/`.
