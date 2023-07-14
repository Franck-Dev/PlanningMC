<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230714135308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outillages ADD projet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE outillages ADD CONSTRAINT FK_73168087C18272 FOREIGN KEY (projet_id) REFERENCES prog_avions (id)');
        $this->addSql('CREATE INDEX IDX_73168087C18272 ON outillages (projet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outillages DROP FOREIGN KEY FK_73168087C18272');
        $this->addSql('DROP INDEX IDX_73168087C18272 ON outillages');
        $this->addSql('ALTER TABLE outillages DROP projet_id');
    }
}
