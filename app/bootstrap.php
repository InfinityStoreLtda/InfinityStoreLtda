<?php
namespace App;
use PDO; use Exception;

class Bootstrap {
  public PDO $db;
  public array $config;
  public function __construct(array $config) {
    $this->config = $config;
    $db = $config['db'];
    $this->db = new PDO($db['dsn'], $db['user'], $db['pass'], $db['options']);
    $this->db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
  }
}
