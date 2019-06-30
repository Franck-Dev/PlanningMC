<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181014201710 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conf_ssmenu ADD id_smenu1 VARCHAR(255) DEFAULT NULL, ADD id_smenu2 VARCHAR(255) DEFAULT NULL, ADD id_smenu3 VARCHAR(255) DEFAULT NULL, ADD id_smenu4 VARCHAR(255) DEFAULT NULL, ADD id_smenu5 VARCHAR(255) DEFAULT NULL, ADD id_smenu6 VARCHAR(255) DEFAULT NULL, ADD id_smenu7 VARCHAR(255) DEFAULT NULL, DROP id_ssmenu1, DROP id_ssmenu2, DROP id_ssmenu3, DROP id_ssmenu4, DROP id_ssmenu5, DROP id_ssmenu6, DROP id_ssmenu7');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE conf_ssmenu ADD id_ssmenu1 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD id_ssmenu2 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD id_ssmenu3 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD id_ssmenu4 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD id_ssmenu5 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD id_ssmenu6 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD id_ssmenu7 VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, DROP id_smenu1, DROP id_smenu2, DROP id_smenu3, DROP id_smenu4, DROP id_smenu5, DROP id_smenu6, DROP id_smenu7');
    }
}
