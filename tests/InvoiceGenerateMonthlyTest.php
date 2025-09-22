<?php

declare(strict_types=1);

namespace Tests;

use App\Bootstrap;
use App\Models\Invoice;
use PDO;
use PHPUnit\Framework\TestCase;

class InvoiceGenerateMonthlyTest extends TestCase
{
    private TestBootstrap $app;

    private Invoice $invoices;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = new TestBootstrap();
        $this->createSchema($this->app->db);
        $this->invoices = new Invoice($this->app);
    }

    public function testCreatesInvoicesForEligibleContracts(): void
    {
        $competencia = '2024-05-01';

        $contratoComValorMensal = $this->insertContract([
            'valor_mensal' => 100.0,
        ]);

        $contratoComItens = $this->insertContract([
            'valor_mensal' => null,
        ]);

        $this->insertItem($contratoComItens, 2, 150.0, true);
        $this->insertItem($contratoComItens, 1, 80.0, true);

        $created = $this->invoices->generateMonthly($competencia);

        $this->assertSame(2, $created);

        $faturas = $this->app->db->query('SELECT contrato_id, competencia, valor_total FROM faturas ORDER BY contrato_id')->fetchAll();
        $this->assertCount(2, $faturas);

        $this->assertSame($contratoComValorMensal, (int) $faturas[0]['contrato_id']);
        $this->assertSame($competencia, $faturas[0]['competencia']);
        $this->assertSame(100.0, (float) $faturas[0]['valor_total']);

        $this->assertSame($contratoComItens, (int) $faturas[1]['contrato_id']);
        $this->assertSame($competencia, $faturas[1]['competencia']);
        $this->assertSame(380.0, (float) $faturas[1]['valor_total']);

        $transacoes = $this->app->db->query('SELECT descricao, valor FROM transacoes ORDER BY contrato_id')->fetchAll();
        $this->assertCount(2, $transacoes);
        $this->assertSame('Fatura 05/2024', $transacoes[0]['descricao']);
        $this->assertSame(100.0, (float) $transacoes[0]['valor']);
        $this->assertSame('Fatura 05/2024', $transacoes[1]['descricao']);
        $this->assertSame(380.0, (float) $transacoes[1]['valor']);
    }

    public function testDoesNotCreateDuplicateInvoicesForSameCompetencia(): void
    {
        $competencia = '2024-06-01';
        $contrato = $this->insertContract(['valor_mensal' => 240.0]);

        $this->app->db->prepare('INSERT INTO faturas(contrato_id, competencia, valor_total) VALUES(:c, :comp, :v)')
            ->execute([
                ':c' => $contrato,
                ':comp' => $competencia,
                ':v' => 240.0,
            ]);

        $created = $this->invoices->generateMonthly($competencia);

        $this->assertSame(0, $created);

        $faturas = $this->app->db->query('SELECT COUNT(*) AS total FROM faturas WHERE contrato_id = ' . $contrato)->fetch();
        $this->assertSame(1, (int) $faturas['total']);

        $transacoes = $this->app->db->query('SELECT COUNT(*) AS total FROM transacoes')->fetch();
        $this->assertSame(0, (int) $transacoes['total']);
    }

    public function testCalculatesTotalFromActiveItemsWhenValorMensalIsMissing(): void
    {
        $competencia = '2024-07-01';
        $contrato = $this->insertContract(['valor_mensal' => null]);

        $this->insertItem($contrato, 5, 12.5, true);
        $this->insertItem($contrato, 2, 100.0, false);

        $created = $this->invoices->generateMonthly($competencia);

        $this->assertSame(1, $created);

        $fatura = $this->app->db->query('SELECT valor_total FROM faturas WHERE contrato_id = ' . $contrato)->fetch();
        $this->assertSame(62.5, (float) $fatura['valor_total']);

        $transacao = $this->app->db->query('SELECT valor FROM transacoes WHERE contrato_id = ' . $contrato)->fetch();
        $this->assertSame(62.5, (float) $transacao['valor']);
    }

    private function createSchema(PDO $db): void
    {
        $db->exec(<<<SQL
            CREATE TABLE contratos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                status TEXT NOT NULL,
                vigencia_inicio TEXT NOT NULL,
                vigencia_fim TEXT NULL,
                valor_mensal REAL NULL,
                empresa_pagadora_id INTEGER NOT NULL,
                centro_custo_id INTEGER NOT NULL
            );
        SQL);

        $db->exec(<<<SQL
            CREATE TABLE contratos_itens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contrato_id INTEGER NOT NULL,
                qtd REAL NOT NULL,
                valor_unit REAL NOT NULL,
                ativo INTEGER NOT NULL
            );
        SQL);

        $db->exec(<<<SQL
            CREATE TABLE faturas (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contrato_id INTEGER NOT NULL,
                competencia TEXT NOT NULL,
                valor_total REAL NOT NULL,
                status TEXT NULL,
                nf_tipo TEXT NULL,
                nf_id INTEGER NULL
            );
        SQL);

        $db->exec('CREATE UNIQUE INDEX faturas_contrato_competencia_unique ON faturas(contrato_id, competencia);');

        $db->exec(<<<SQL
            CREATE TABLE transacoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                data TEXT NOT NULL,
                tipo TEXT NOT NULL,
                contrato_id INTEGER NOT NULL,
                empresa_pagadora_id INTEGER NOT NULL,
                centro_custo_id INTEGER NOT NULL,
                categoria TEXT NOT NULL,
                descricao TEXT NOT NULL,
                valor REAL NOT NULL,
                status_pagamento TEXT NOT NULL,
                parcela_numero INTEGER NOT NULL,
                parcela_total INTEGER NOT NULL
            );
        SQL);
    }

    private function insertContract(array $overrides = []): int
    {
        $data = array_merge([
            'status' => 'Vigente',
            'vigencia_inicio' => '2024-01-01',
            'vigencia_fim' => null,
            'valor_mensal' => 0.0,
            'empresa_pagadora_id' => 1,
            'centro_custo_id' => 1,
        ], $overrides);

        $stmt = $this->app->db->prepare(
            'INSERT INTO contratos(status, vigencia_inicio, vigencia_fim, valor_mensal, empresa_pagadora_id, centro_custo_id)
             VALUES (:status, :inicio, :fim, :valor, :empresa, :centro)'
        );

        $stmt->execute([
            ':status' => $data['status'],
            ':inicio' => $data['vigencia_inicio'],
            ':fim' => $data['vigencia_fim'],
            ':valor' => $data['valor_mensal'],
            ':empresa' => $data['empresa_pagadora_id'],
            ':centro' => $data['centro_custo_id'],
        ]);

        return (int) $this->app->db->lastInsertId();
    }

    private function insertItem(int $contratoId, float $qtd, float $valorUnit, bool $ativo): void
    {
        $stmt = $this->app->db->prepare(
            'INSERT INTO contratos_itens(contrato_id, qtd, valor_unit, ativo) VALUES(:contrato, :qtd, :valor, :ativo)'
        );

        $stmt->execute([
            ':contrato' => $contratoId,
            ':qtd' => $qtd,
            ':valor' => $valorUnit,
            ':ativo' => $ativo ? 1 : 0,
        ]);
    }
}

class TestBootstrap extends Bootstrap
{
    public function __construct()
    {
        $this->config = ['db' => ['dsn' => 'sqlite::memory:']];
        $this->db = new TestPDO('sqlite::memory:');
    }
}

class TestPDO extends PDO
{
    private bool $lastInsertSucceeded = false;

    public function __construct(string $dsn)
    {
        parent::__construct($dsn);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, [TestPDOStatement::class, [$this]]);
    }

    public function prepare(string $statement, array $options = []): \PDOStatement|false
    {
        $statement = str_replace('INSERT IGNORE', 'INSERT OR IGNORE', $statement);
        $statement = str_replace("'Fatura :mes'", "'Fatura ' || :mes", $statement);
        $stmt = parent::prepare($statement, $options);

        if ($stmt instanceof TestPDOStatement) {
            $stmt->setOriginSql($statement);
        }

        return $stmt;
    }

    public function exec(string $statement): int|false
    {
        $statement = str_replace('INSERT IGNORE', 'INSERT OR IGNORE', $statement);
        $statement = str_replace("'Fatura :mes'", "'Fatura ' || :mes", $statement);
        $result = parent::exec($statement);

        if (\str_starts_with(\strtoupper(\ltrim($statement)), 'INSERT')) {
            $this->lastInsertSucceeded = $result > 0;
        }

        return $result;
    }

    public function setLastInsertSucceeded(bool $value): void
    {
        $this->lastInsertSucceeded = $value;
    }

    public function lastInsertId($name = null): string|false
    {
        if (!$this->lastInsertSucceeded) {
            return '0';
        }

        return parent::lastInsertId($name);
    }
}

class TestPDOStatement extends \PDOStatement
{
    private TestPDO $connection;

    private string $sql = '';

    protected function __construct(TestPDO $connection)
    {
        $this->connection = $connection;
    }

    public function setOriginSql(string $sql): void
    {
        $this->sql = $sql;
    }

    public function execute(?array $params = null): bool
    {
        $result = parent::execute($params ?? []);

        if (\str_starts_with(\strtoupper(\ltrim($this->sql)), 'INSERT')) {
            $this->connection->setLastInsertSucceeded($this->rowCount() > 0);
        }

        return $result;
    }
}
