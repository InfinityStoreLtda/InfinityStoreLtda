<?php
namespace App\Controllers;

use App\Bootstrap;
use App\Models\Contract;

class ContractsController {
  private Contract $contracts;

  public function __construct(private Bootstrap $app) {
    $this->contracts = new Contract($app);
  }

  public function index(): void {
    $rows = $this->contracts->paginate(1, 50);
    $this->render('contracts/index', compact('rows'));
  }

  public function show(string $id): void {
    $contractId = (int) $id;
    $c = $this->contracts->find($contractId);
    if (!$c) {
      http_response_code(404);
      echo 'Contrato não encontrado';
      return;
    }
    $items = $this->contracts->items($contractId);
    $this->render('contracts/show', compact('c', 'items'));
  }

  public function create(): void {
    $this->render('contracts/create');
  }

  public function store(): void {
    $id = $this->contracts->create($_POST);
    header('Location: /contracts/' . $id);
    exit;
  }

  public function edit(string $id): void {
    $c = $this->contracts->find((int) $id);
    if (!$c) {
      http_response_code(404);
      echo 'Contrato não encontrado';
      return;
    }
    $this->render('contracts/edit', compact('c'));
  }

  public function update(string $id): void {
    $this->contracts->update((int) $id, $_POST);
    header('Location: /contracts/' . $id);
  }

  public function addItem(string $id): void {
    $this->contracts->addItem((int) $id, $_POST);
    header('Location: /contracts/' . $id);
  }

  private function render(string $view, array $data = []): void {
    extract($data);
    $viewPath = __DIR__ . '/../../views/' . $view . '.php';
    if (!file_exists($viewPath)) {
      throw new \RuntimeException('View não encontrada: ' . $view);
    }
    include $viewPath;
  }
}
