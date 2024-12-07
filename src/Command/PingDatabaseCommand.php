<?php

namespace App\Command;

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:ping-database',
    description: 'Add a short description for your command',
)]
class PingDatabaseCommand extends Command
{
    protected static $defaultName = 'app:ping-database';

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription("Executar uma consulta para arquecimento do banco de dados.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try{
            $connection = DriverManager::getConnection(['url' =>$_ENV['DATABASE_URL']]);
            $stmt = $connection->prepare("SELECT 1");
            $result = $stmt->executeQuery();

            $row = $result->fetchAssociative();
            if ($row) {
                $output->writeln('Banco de dados ativo!');
                return Command::SUCCESS;
            }

            $output->writeln('Falha ao pingar o banco de dados');
            return Command::FAILURE;
        }catch(\Exception $e){
            $output->writeln('Erro ao pingar o banco de dados:'. $e->getMessage());
            return Command::FAILURE;
        }
    }
}
