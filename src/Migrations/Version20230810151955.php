<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230810151955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prog_moyens_moyens (prog_moyens_id INT NOT NULL, moyens_id INT NOT NULL, INDEX IDX_6FA19F4C9ECABC3 (prog_moyens_id), INDEX IDX_6FA19F4A5AF1127 (moyens_id), PRIMARY KEY(prog_moyens_id, moyens_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prog_moyens_moyens ADD CONSTRAINT FK_6FA19F4C9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prog_moyens_moyens ADD CONSTRAINT FK_6FA19F4A5AF1127 FOREIGN KEY (moyens_id) REFERENCES moyens (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prog_moyens_moyens DROP FOREIGN KEY FK_6FA19F4C9ECABC3');
        $this->addSql('ALTER TABLE prog_moyens_moyens DROP FOREIGN KEY FK_6FA19F4A5AF1127');
        $this->addSql('DROP TABLE prog_moyens_moyens');
    }
}
