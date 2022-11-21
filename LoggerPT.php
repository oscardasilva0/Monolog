<?php

namespace Patmed;

use MonologPHPMailer\PHPMailerHandler;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Patmed\DBHandler as DBHandler;

use PHPMailer\PHPMailer\PHPMailer;

class LoggerPT
{
  private $logger;
  private $mailer;
  private $messageDadosEnvio;
  private $sqliteRoute;
  private $class;

  public function __construct(string $class)
  {
    $this->class = $class;

    $mailer = new PHPMailer(true);

    $mailer->isSMTP();
    $mailer->Host = 'alegria.mednet.com.br';
    $mailer->SMTPAuth = true;
    $mailer->Username = 'patmed@patmed.com.br';
    $mailer->Password = 'demtap&*(0';
    $mailer->Port = 587;

    $mailer->setFrom('patmed@patmed.com.br', 'Logger patmed');
    $mailer->addAddress('oscardasilva52@gmail.com', 'SYSDBA');
    $this->mailer =  &$mailer;
    $this->logger = new Logger('logger');
  }

  public function enviarEmail(string $messageTexto): void
  {
    $this->logger->pushProcessor(new IntrospectionProcessor);
    $this->logger->pushProcessor(new MemoryUsageProcessor);
    $this->logger->pushProcessor(new WebProcessor);

    $handler = new PHPMailerHandler($this->mailer);
    $handler->setFormatter(new HtmlFormatter);

    $this->logger->pushHandler($handler);

    $this->logger->error($messageTexto);
  }

  public function sqlite(string $messageTexto): void
  {
    $db_handler = new DBHandler(new \PDO($this->sqliteRoute));
    die;
    $this->logger->pushHandler($db_handler);
    $this->logger->debug($messageTexto);
  }

  public function log(string $messageTexto, int $logger = Logger::INFO): void
  {
    $this->logger->pushProcessor(new IntrospectionProcessor);
    $this->logger->pushProcessor(new MemoryUsageProcessor);
    $this->logger->pushProcessor(new WebProcessor);
    $this->logger->pushHandler(new StreamHandler(__DIR__ . '/' . $this->class . '.log', $logger));
    $this->logger->pushHandler(new FirePHPHandler());

    // You can now use your logger
    $this->logger->info($messageTexto);
  }
}
