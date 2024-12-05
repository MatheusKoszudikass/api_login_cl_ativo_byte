<?php

namespace App\Command;

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
    private $connection;
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
            $this->connection->execyteQuery('SELECT 1');
            $output->writeln('banco de dados ativo');
            return Command::SUCCESS;
        }catch(\Exception $e){
            $output->writeln('Erro ao pingar o banco de dados:'. $e->getMessage());
            return Command::FAILURE;
        }
    }
}
