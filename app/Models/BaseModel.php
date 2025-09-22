<?php
namespace App\Models;
use App\Bootstrap; use PDO;

abstract class BaseModel {
  protected PDO $db;
  public function __construct(protected Bootstrap $app){ $this->db = $app->db; }
}
