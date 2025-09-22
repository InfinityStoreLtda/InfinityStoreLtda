<?php
namespace App\Controllers;
use App\Bootstrap; use App\Models\Contract;

class ContractsController {
  private Contract $contracts;
  public function __construct(private Bootstrap $app){ $this->contracts = new Contract($app); }
  public function index(){
    $rows = $this->contracts->paginate(1,50);
    include __DIR__.'/../../views/contracts/index.php';
  }
  public function show($id){
    $c = $this->contracts->find((int)$id);
    $items = $this->contracts->items((int)$id);
    include __DIR__.'/../../views/contracts/show.php';
  }
  public function create(){ include __DIR__.'/../../views/contracts/create.php'; }
  public function store(){
    $id = $this->contracts->create($_POST);
    header('Location: /contracts/'.$id); exit;
  }
  public function edit($id){ $c=$this->contracts->find((int)$id); include __DIR__.'/../../views/contracts/edit.php'; }
  public function update($id){ $this->contracts->update((int)$id, $_POST); header('Location: /contracts/'.$id); }
  public function addItem($id){ $this->contracts->addItem((int)$id, $_POST); header('Location: /contracts/'.$id); }
}
