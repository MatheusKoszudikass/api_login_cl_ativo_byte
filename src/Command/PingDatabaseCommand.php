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
        try
        {
            $urls = [
                'https://api.cliente.ativobyte.com.br/api/auth/verify',
                'https://api.cliente.ativobyte.com.br/api/doc'
            ];

            foreach($urls as $url)
            {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url); 
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                if($response)
                {
                    $output->writeln("Site pingado com sucesso.". curl_getinfo($ch, CURLINFO_HTTP_CODE));
                    return Command::SUCCESS;
                }

                curl_close($ch);
            }

            $output->writeln('Falha ao pingar.');
            return Command::FAILURE;

        }catch(\Exception $e)
        {
            $output->writeln('Erro ao pingar:'. $e->getMessage());
            return Command::FAILURE;
        }
    }
}
