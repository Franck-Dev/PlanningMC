<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190721193845 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD14049DEFD00 FOREIGN KEY (polym_plannif_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD140A5AF1127 FOREIGN KEY (moyens_id) REFERENCES moyens (id)');
        $this->addSql('ALTER TABLE polym_real ADD CONSTRAINT FK_D14CD140A0A1C920 FOREIGN KEY (programmes_id) REFERENCES prog_moyens (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D14CD14049DEFD00 ON polym_real (polym_plannif_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D14CD140A5AF1127 ON polym_real (moyens_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D14CD140A0A1C920 ON polym_real (programmes_id)');
        $this->addSql('ALTER TABLE user ADD isactivate TINYINT(1) NOT NULL, CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD14049DEFD00');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD140A5AF1127');
        $this->addSql('ALTER TABLE polym_real DROP FOREIGN KEY FK_D14CD140A0A1C920');
        $this->addSql('DROP INDEX UNIQ_D14CD14049DEFD00 ON polym_real');
        $this->addSql('DROP INDEX UNIQ_D14CD140A5AF1127 ON polym_real');
        $this->addSql('DROP INDEX UNIQ_D14CD140A0A1C920 ON polym_real');
        $this->addSql('ALTER TABLE user DROP isactivate, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}