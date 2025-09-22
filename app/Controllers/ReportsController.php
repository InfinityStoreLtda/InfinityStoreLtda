<?php
namespace App\Controllers;
use App\Bootstrap; use Dompdf\Dompdf; use PhpOffice\PhpSpreadsheet\Spreadsheet; use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportsController {
  public function __construct(private Bootstrap $app){}
  public function costs(){
    $y = (int)($_GET['ano'] ?? date('Y')); $m = (int)($_GET['mes'] ?? date('n'));
    $stmt = $this->app->db->prepare("SELECT * FROM vw_custos_por_cliente_mes WHERE ano=:y AND mes=:m ORDER BY cliente, centro_custo");
    $stmt->execute([':y'=>$y, ':m'=>$m]); $rows=$stmt->fetchAll();
    include __DIR__.'/../../views/reports/costs.php';
  }
  public function contracts(){
    $rows = $this->app->db->query("SELECT * FROM vw_contratos_vigentes")->fetchAll();
    include __DIR__.'/../../views/reports/contracts.php';
  }
  public function exportCostsPDF(){
    $y=(int)($_GET['ano']??date('Y')); $m=(int)($_GET['mes']??date('n'));
    $stmt=$this->app->db->prepare("SELECT * FROM vw_custos_por_cliente_mes WHERE ano=:y AND mes=:m ORDER BY cliente, centro_custo");
    $stmt->execute([':y'=>$y, ':m'=>$m]); $rows=$stmt->fetchAll();
    $html = $this->renderCostsHTML($rows,$y,$m);
    $dompdf = new Dompdf(); $dompdf->loadHtml($html); $dompdf->render();
    header('Content-Type: application/pdf'); header('Content-Disposition: attachment; filename="custos-'.$y.'-'.$m.'.pdf"');
    echo $dompdf->output();
  }
  public function exportCostsXLS(){
    $y=(int)($_GET['ano']??date('Y')); $m=(int)($_GET['mes']??date('n'));
    $stmt=$this->app->db->prepare("SELECT * FROM vw_custos_por_cliente_mes WHERE ano=:y AND mes=:m ORDER BY cliente, centro_custo");
    $stmt->execute([':y'=>$y, ':m'=>$m]); $rows=$stmt->fetchAll();
    $ss = new Spreadsheet(); $sheet=$ss->getActiveSheet();
    $sheet->fromArray(['Cliente','Centro de Custo','Receita','Despesa'], NULL, 'A1');
    $r=2; foreach($rows as $row){
      $sheet->setCellValue('A'.$r, $row['cliente']);
      $sheet->setCellValue('B'.$r, $row['centro_custo']);
      $sheet->setCellValue('C'.$r, (float)$row['total_receita']);
      $sheet->setCellValue('D'.$r, (float)$row['total_despesa']);
      $r++;
    }
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="custos-'.$y.'-'.$m.'.xlsx"');
    $writer=new Xlsx($ss); $writer->save('php://output');
  }
  private function renderCostsHTML(array $rows,int $y,int $m): string{
    ob_start(); ?>
    <html><head><meta charset="utf-8"><style>
      body{font-family: DejaVu Sans, sans-serif; font-size:12px}
      h1{font-size:16px}
      table{width:100%; border-collapse:collapse}
      th,td{border:1px solid #ccc; padding:6px}
    </style></head><body>
    <h1>Relatório de Custos — <?=sprintf('%02d/%d',$m,$y)?></h1>
    <table><thead><tr><th>Cliente</th><th>Centro de Custo</th><th>Receita</th><th>Despesa</th></tr></thead><tbody>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><?=htmlspecialchars($r['cliente']??'-')?></td>
        <td><?=htmlspecialchars($r['centro_custo']??'-')?></td>
        <td style="text-align:right;">R$ <?=number_format((float)$r['total_receita'],2,',','.')?></td>
        <td style="text-align:right;">R$ <?=number_format((float)$r['total_despesa'],2,',','.')?></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></body></html>
    <?php return ob_get_clean();
  }
}
