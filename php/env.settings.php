<?php
  $variables = [
      'APP_KEY' => '937a4a8c13e317dfd28effdd479cad2f',
      'DB_HOST' => '192.168.11.103',
      'DB_USERNAME' => 'root',
      'DB_PASSWORD' => 'root',
      'DB_NAME' => 'cicmonitoring',
      'DB_PORT' => '8082',
  ];

  foreach ($variables as $key => $value) {
      putenv("$key=$value");
  }

?>