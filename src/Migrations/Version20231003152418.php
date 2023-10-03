<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231003152418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chargement ADD tps_charge_ot VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', ADD tx_charge_ot SMALLINT DEFAULT NULL, CHANGE id_planning id_planning INT NOT NULL');
        }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chargement DROP tps_charge_ot, DROP tx_charge_ot, CHANGE id_planning id_planning INT DEFAULT NULL');
        }
}
