<?php


namespace Tests\Dependency;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

 class DatabaseTestCase extends KernelTestCase
{
    private EntityManagerInterface $_entityManager;

    public function setUp(): void
    {
        self::bootKernel();

        $this->_entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        
        $schemaTool = new SchemaTool($this->_entityManager);

        $metadata= $this->_entityManager->getMetadataFactory()->getAllMetadata();

        if(empty($metadata))
            throw new \Exception("Metadata is empty");

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->_entityManager->close();
        unset($this->_entityManager);
    }
}