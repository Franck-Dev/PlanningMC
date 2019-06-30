<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181014123837 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conf_smenu CHANGE id_smenu1 id_smenu1 VARCHAR(255) DEFAULT NULL, CHANGE id_smenu2 id_smenu2 VARCHAR(255) DEFAULT NULL, CHANGE id_smenu3 id_smenu3 VARCHAR(255) DEFAULT NULL, CHANGE id_smenu4 id_smenu4 VARCHAR(255) DEFAULT NULL, CHANGE id_smenu5 id_smenu5 VARCHAR(255) DEFAULT NULL, CHANGE id_smenu6 id_smenu6 VARCHAR(255) DEFAULT NULL, CHANGE id_smenu7 id_smenu7 VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conf_smenu CHANGE id_smenu1 id_smenu1 INT DEFAULT NULL, CHANGE id_smenu2 id_smenu2 INT DEFAULT NULL, CHANGE id_smenu3 id_smenu3 INT DEFAULT NULL, CHANGE id_smenu4 id_smenu4 INT DEFAULT NULL, CHANGE id_smenu5 id_smenu5 INT DEFAULT NULL, CHANGE id_smenu6 id_smenu6 INT DEFAULT NULL, CHANGE id_smenu7 id_smenu7 INT DEFAULT NULL');
    }
}
