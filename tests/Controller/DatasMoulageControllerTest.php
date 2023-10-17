<?php

namespace App\Test\Controller;

use App\Entity\DatasMoulage;
use App\Repository\DatasMoulageRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DatasMoulageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DatasMoulageRepository $repository;
    private string $path = '/moulage/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(DatasMoulage::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DatasMoulage index');

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
            'datas_moulage[artGamm]' => 'Testing',
            'datas_moulage[DescriptifPhase]' => 'Testing',
            'datas_moulage[cycleMoulage]' => 'Testing',
            'datas_moulage[active]' => 'Testing',
            'datas_moulage[debVal]' => 'Testing',
            'datas_moulage[zoneTrav]' => 'Testing',
            'datas_moulage[eqOut]' => 'Testing',
            'datas_moulage[progLaser]' => 'Testing',
            'datas_moulage[progPolym]' => 'Testing',
            'datas_moulage[programme]' => 'Testing',
        ]);

        self::assertResponseRedirects('/moulage/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new DatasMoulage();
        $fixture->setArtGamm('My Title');
        $fixture->setDescriptifPhase('My Title');
        $fixture->setCycleMoulage('My Title');
        $fixture->setActive('My Title');
        $fixture->setDebVal('My Title');
        $fixture->setZoneTrav('My Title');
        $fixture->setEqOut('My Title');
        $fixture->setProgLaser('My Title');
        $fixture->setProgPolym('My Title');
        $fixture->setProgramme('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DatasMoulage');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new DatasMoulage();
        $fixture->setArtGamm('My Title');
        $fixture->setDescriptifPhase('My Title');
        $fixture->setCycleMoulage('My Title');
        $fixture->setActive('My Title');
        $fixture->setDebVal('My Title');
        $fixture->setZoneTrav('My Title');
        $fixture->setEqOut('My Title');
        $fixture->setProgLaser('My Title');
        $fixture->setProgPolym('My Title');
        $fixture->setProgramme('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'datas_moulage[artGamm]' => 'Something New',
            'datas_moulage[DescriptifPhase]' => 'Something New',
            'datas_moulage[cycleMoulage]' => 'Something New',
            'datas_moulage[active]' => 'Something New',
            'datas_moulage[debVal]' => 'Something New',
            'datas_moulage[zoneTrav]' => 'Something New',
            'datas_moulage[eqOut]' => 'Something New',
            'datas_moulage[progLaser]' => 'Something New',
            'datas_moulage[progPolym]' => 'Something New',
            'datas_moulage[programme]' => 'Something New',
        ]);

        self::assertResponseRedirects('/moulage/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getArtGamm());
        self::assertSame('Something New', $fixture[0]->getDescriptifPhase());
        self::assertSame('Something New', $fixture[0]->getCycleMoulage());
        self::assertSame('Something New', $fixture[0]->getActive());
        self::assertSame('Something New', $fixture[0]->getDebVal());
        self::assertSame('Something New', $fixture[0]->getZoneTrav());
        self::assertSame('Something New', $fixture[0]->getEqOut());
        self::assertSame('Something New', $fixture[0]->getProgLaser());
        self::assertSame('Something New', $fixture[0]->getProgPolym());
        self::assertSame('Something New', $fixture[0]->getProgramme());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new DatasMoulage();
        $fixture->setArtGamm('My Title');
        $fixture->setDescriptifPhase('My Title');
        $fixture->setCycleMoulage('My Title');
        $fixture->setActive('My Title');
        $fixture->setDebVal('My Title');
        $fixture->setZoneTrav('My Title');
        $fixture->setEqOut('My Title');
        $fixture->setProgLaser('My Title');
        $fixture->setProgPolym('My Title');
        $fixture->setProgramme('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/moulage/');
    }
}
