<?php
  $variables = [
      'APP_KEY' => '937a4a8c13e317dfd28effdd479cad2f',
      'DB_HOST' => '192.168.11.39',
      'DB_USERNAME' => 'helpdesk',
      'DB_DSN' => 'mysql:host=192.168.11.39;port:3307;dbname=dev_cicmonitoring;charset=utf8',
      'DB_PASSWORD' => 'citihelpdesk',
      'DB_NAME' => 'dev_cicmonitoring',
      'DB_PORT' => '3307',
  ];

//   $variables = [
//     'APP_KEY' => '937a4a8c13e317dfd28effdd479cad2f',
//     'DB_HOST' => 'localhost',
//     'DB_USERNAME' => 'root',
//     'DB_DSN' => 'mysql:host=localhost;dbname=practice;charset=utf8',
//     'DB_PASSWORD' => '',
//     'DB_NAME' => 'cicmonitoring',
//     'DB_PORT' => '8082',
// ];

  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }
  
?>