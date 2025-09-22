<?php
namespace App\Models;

class FiscalNote extends BaseModel {
  public function create(array $nf){
    $sql="INSERT INTO notas_fiscais(tipo,numero,serie,chave,protocolo,data_emissao,status,xml_path,pdf_path,retorno)
          VALUES(:tipo,:numero,:serie,:chave,:proto,:emissao,:status,:xml,:pdf,:ret)";
    $this->db->prepare($sql)->execute([
      ':tipo'=>$nf['tipo'], ':numero'=>$nf['numero']??null, ':serie'=>$nf['serie']??null,
      ':chave'=>$nf['chave']??null, ':proto'=>$nf['protocolo']??null,
      ':emissao'=>$nf['data_emissao']??null, ':status'=>$nf['status']??'Pendente',
      ':xml'=>$nf['xml_path']??null, ':pdf'=>$nf['pdf_path']??null, ':ret'=>$nf['retorno']??null
    ]);
    return (int)$this->db->lastInsertId();
  }
}
