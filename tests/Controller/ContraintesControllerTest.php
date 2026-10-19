<?php

namespace App\Test\Controller;

use App\Entity\Contraintes;
use App\Repository\ContraintesRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContraintesControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ContraintesRepository $repository;
    private string $path = '/contraintes/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Contraintes::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Contrainte index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'contrainte[Libelle]' => 'Testing',
            'contrainte[Description]' => 'Testing',
            'contrainte[impact]' => 'Testing',
            'contrainte[service]' => 'Testing',
        ]);

        self::assertResponseRedirects('/contraintes/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Contraintes();
        $fixture->setLibelle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setImpact('My Title');
        $fixture->setService('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Contrainte');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Contraintes();
        $fixture->setLibelle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setImpact('My Title');
        $fixture->setService('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'contrainte[Libelle]' => 'Something New',
            'contrainte[Description]' => 'Something New',
            'contrainte[impact]' => 'Something New',
            'contrainte[service]' => 'Something New',
        ]);

        self::assertResponseRedirects('/contraintes/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLibelle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getImpact());
        self::assertSame('Something New', $fixture[0]->getService());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Contraintes();
        $fixture->setLibelle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setImpact('My Title');
        $fixture->setService('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/contraintes/');
    }
}
