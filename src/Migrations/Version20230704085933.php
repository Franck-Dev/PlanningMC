<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704085933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE demandes_outillages (demandes_id INT NOT NULL, outillages_id INT NOT NULL, INDEX IDX_AC41B9F2F49DCC2D (demandes_id), INDEX IDX_AC41B9F24D6CE55C (outillages_id), PRIMARY KEY(demandes_id, outillages_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demandes_outillages ADD CONSTRAINT FK_AC41B9F2F49DCC2D FOREIGN KEY (demandes_id) REFERENCES demandes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demandes_outillages ADD CONSTRAINT FK_AC41B9F24D6CE55C FOREIGN KEY (outillages_id) REFERENCES outillages (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demandes_outillages DROP FOREIGN KEY FK_AC41B9F2F49DCC2D');
        $this->addSql('ALTER TABLE demandes_outillages DROP FOREIGN KEY FK_AC41B9F24D6CE55C');
        $this->addSql('DROP TABLE demandes_outillages');
    }
}
