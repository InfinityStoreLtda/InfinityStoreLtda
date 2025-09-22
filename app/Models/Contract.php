<?php
namespace App\Models;

class Contract extends BaseModel {
  public function paginate(int $page = 1, int $per = 20): array {
    $off = ($page - 1) * $per;
    $stmt = $this->db->prepare("SELECT c.*, ep.nome_empresa AS cliente, cc.nome AS centro_custo
      FROM contratos c
      JOIN empresas_pagadoras ep ON ep.id=c.empresa_pagadora_id
      LEFT JOIN centros_custo cc ON cc.id=c.centro_custo_id
      ORDER BY c.created_at DESC LIMIT :per OFFSET :off");
    $stmt->bindValue(':per', $per, \PDO::PARAM_INT);
    $stmt->bindValue(':off', $off, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function find(int $id): ?array {
    $stmt = $this->db->prepare("SELECT c.*, ep.nome_empresa AS cliente, cc.nome AS centro_custo
      FROM contratos c
      JOIN empresas_pagadoras ep ON ep.id=c.empresa_pagadora_id
      LEFT JOIN centros_custo cc ON cc.id=c.centro_custo_id WHERE c.id=:id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row !== false ? $row : null;
  }

  public function items(int $id): array {
    $stmt = $this->db->prepare("SELECT * FROM contratos_itens WHERE contrato_id=:id AND ativo=1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll();
  }

  public function create(array $data): int {
    $sql = "INSERT INTO contratos(empresa_pagadora_id,numero,processo,empenho,vigencia_inicio,vigencia_fim,centro_custo_id,valor_mensal)
            VALUES(:emp,:num,:proc,:empn,:vi,:vf,:cc,:vm)";
    $this->db->prepare($sql)->execute([
      ':emp' => $data['empresa_pagadora_id'], ':num' => $data['numero'] ?? null,
      ':proc' => $data['processo'] ?? null, ':empn' => $data['empenho'] ?? null,
      ':vi' => $data['vigencia_inicio'], ':vf' => $data['vigencia_fim'] ?? null,
      ':cc' => $data['centro_custo_id'] ?? null, ':vm' => $data['valor_mensal'] ?? null,
    ]);
    return (int) $this->db->lastInsertId();
  }

  public function update(int $id, array $data): bool {
    $sql = "UPDATE contratos SET numero=:num, processo=:proc, empenho=:empn, vigencia_inicio=:vi, vigencia_fim=:vf, centro_custo_id=:cc, valor_mensal=:vm WHERE id=:id";
    return $this->db->prepare($sql)->execute([
      ':num' => $data['numero'] ?? null, ':proc' => $data['processo'] ?? null, ':empn' => $data['empenho'] ?? null,
      ':vi' => $data['vigencia_inicio'], ':vf' => $data['vigencia_fim'] ?? null,
      ':cc' => $data['centro_custo_id'] ?? null, ':vm' => $data['valor_mensal'] ?? null, ':id' => $id
    ]);
  }

  public function addItem(int $contratoId, array $item): void {
    $sql = "INSERT INTO contratos_itens(contrato_id,descricao,unidade,qtd,valor_unit,ativo_glpi_id,zabbix_hostid)
            VALUES(:c,:d,:u,:q,:v,:glpi,:zbx)";
    $this->db->prepare($sql)->execute([
      ':c' => $contratoId, ':d' => $item['descricao'], ':u' => $item['unidade'] ?? 'un', ':q' => $item['qtd'] ?? 1,
      ':v' => $item['valor_unit'], ':glpi' => $item['ativo_glpi_id'] ?? null, ':zbx' => $item['zabbix_hostid'] ?? null
    ]);
  }

  public function sumItems(int $contratoId): float {
    $stmt = $this->db->prepare("SELECT SUM(qtd*valor_unit) AS total FROM contratos_itens WHERE contrato_id=:id AND ativo=1");
    $stmt->execute([':id' => $contratoId]);
    $result = $stmt->fetch();
    return (float) ($result['total'] ?? 0);
  }
}
