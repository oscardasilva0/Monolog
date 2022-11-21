<?php

namespace Patmed;

use PDO;
use PDOStatement;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class DBHandler extends AbstractProcessingHandler
{
  private bool $initialized = false;
  private PDO $pdo;
  private PDOStatement $statement;

  public function __construct(PDO $pdo,  $level = Logger::DEBUG, bool $bubble = true)
  {
    $this->pdo = $pdo;
    parent::__construct($level, $bubble);
  }

  protected function write(array $record): void
  {
    if (!$this->initialized) {
      $this->initialize();
    }

    // var_dump($record);
    // die;

    $this->statement->execute(array(
      'channel' => $record['channel'],
      'level' => $record['level_name'],
      'message' => $record['formatted'],
      'time' => $record['datetime']->format('U'),
    ));
  }

  private function initialize()
  {
    $this->pdo->exec(
      'CREATE TABLE IF NOT EXISTS monolog '
        . '(channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
    );
    $this->statement = $this->pdo->prepare(
      'INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)'
    );

    $this->initialized = true;
  }
}
