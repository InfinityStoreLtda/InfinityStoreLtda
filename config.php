<?php
return [
  'db' => [
    'dsn' => 'mysql:host=127.0.0.1;dbname=u824192816_financeiro_inf;charset=utf8mb4',
    'user' => 'SEU_USER',
    'pass' => 'SUA_SENHA',
    'options' => [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ]
  ],
  'app' => [
    'base_url' => '/',           // ajuste se usar subcaminho
    'timezone' => 'America/Porto_Velho'
  ]
];
