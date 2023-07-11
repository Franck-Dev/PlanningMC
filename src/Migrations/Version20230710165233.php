<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710165233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles_prog_moyens (articles_id INT NOT NULL, prog_moyens_id INT NOT NULL, INDEX IDX_8A1483381EBAF6CC (articles_id), INDEX IDX_8A148338C9ECABC3 (prog_moyens_id), PRIMARY KEY(articles_id, prog_moyens_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles_prog_moyens ADD CONSTRAINT FK_8A1483381EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_prog_moyens ADD CONSTRAINT FK_8A148338C9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles_prog_moyens DROP FOREIGN KEY FK_8A1483381EBAF6CC');
        $this->addSql('ALTER TABLE articles_prog_moyens DROP FOREIGN KEY FK_8A148338C9ECABC3');
        $this->addSql('DROP TABLE articles_prog_moyens');
    }
}
