<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230627082846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE moulage_moyens (moulage_id INT NOT NULL, moyens_id INT NOT NULL, INDEX IDX_ABD8B30D91AD7C17 (moulage_id), INDEX IDX_ABD8B30DA5AF1127 (moyens_id), PRIMARY KEY(moulage_id, moyens_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE moulage_moyens ADD CONSTRAINT FK_ABD8B30D91AD7C17 FOREIGN KEY (moulage_id) REFERENCES moulage (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE moulage_moyens ADD CONSTRAINT FK_ABD8B30DA5AF1127 FOREIGN KEY (moyens_id) REFERENCES moyens (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moulage_moyens DROP FOREIGN KEY FK_ABD8B30D91AD7C17');
        $this->addSql('ALTER TABLE moulage_moyens DROP FOREIGN KEY FK_ABD8B30DA5AF1127');
        $this->addSql('DROP TABLE moulage_moyens');
    }
}
