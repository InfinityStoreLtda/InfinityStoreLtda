<?php
namespace App\Models;

use DateTime;

class Invoice extends BaseModel {
  public function list(int $page = 1, int $per = 30): array {
    $off = ($page - 1) * $per;
    $sql = "SELECT f.*, c.numero AS contrato_numero, ep.nome_empresa AS cliente
          FROM faturas f
          JOIN contratos c ON c.id=f.contrato_id
          JOIN empresas_pagadoras ep ON ep.id=c.empresa_pagadora_id
          ORDER BY f.competencia DESC LIMIT :per OFFSET :off";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':per', $per, \PDO::PARAM_INT);
    $stmt->bindValue(':off', $off, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function find(int $id): ?array {
    $stmt = $this->db->prepare("SELECT * FROM faturas WHERE id=:id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row !== false ? $row : null;
  }

  public function generateMonthly(string $competencia): int {
    $sqlC = "SELECT * FROM contratos WHERE status='Vigente' AND vigencia_inicio<=:c AND (vigencia_fim IS NULL OR vigencia_fim>=:c)";
    $stmtC = $this->db->prepare($sqlC);
    $stmtC->execute([':c' => $competencia]);
    $created = 0;
    while ($c = $stmtC->fetch()) {
      $total = $c['valor_mensal'] ?? null;
      if ($total === null) {
        $stmt = $this->db->prepare("SELECT SUM(qtd*valor_unit) AS total FROM contratos_itens WHERE contrato_id=:id AND ativo=1");
        $stmt->execute([':id' => $c['id']]);
        $r = $stmt->fetch();
        $total = (float) ($r['total'] ?? 0);
      }

      $sql = "INSERT IGNORE INTO faturas(contrato_id,competencia,valor_total) VALUES(:id,:comp,:vl)";
      $this->db->prepare($sql)->execute([':id' => $c['id'], ':comp' => $competencia, ':vl' => $total]);

      if ($this->db->lastInsertId()) {
        $sqlL = "INSERT INTO transacoes(data,tipo,contrato_id,empresa_pagadora_id,centro_custo_id,categoria,descricao,valor,status_pagamento,parcela_numero,parcela_total)
               VALUES(:d,'receita',:ct,:cl,:cc,'Fatura',:descricao,:v,'pendente',1,1)";
        $mes = DateTime::createFromFormat('Y-m-d', $competencia)?->format('m/Y') ?? date('m/Y', strtotime($competencia));
        $this->db->prepare($sqlL)->execute([
          ':d' => $competencia, ':ct' => $c['id'], ':cl' => $c['empresa_pagadora_id'], ':cc' => $c['centro_custo_id'],
          ':descricao' => "Fatura {$mes}", ':v' => $total
        ]);
        $created++;
      }
    }
    return $created;
  }

  public function markEmitted(int $id, string $tipo, int $nfId): void {
    $sql = "UPDATE faturas SET status='Emitida', nf_tipo=:t, nf_id=:nf WHERE id=:id";
    $this->db->prepare($sql)->execute([':t' => $tipo, ':nf' => $nfId, ':id' => $id]);
  }

  public function settle(int $id): void {
    $this->db->prepare("UPDATE faturas SET status='Paga' WHERE id=:id")->execute([':id' => $id]);
  }
}
