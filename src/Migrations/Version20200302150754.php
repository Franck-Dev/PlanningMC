<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302150754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE outillages_prog_moyens (outillages_id INT NOT NULL, prog_moyens_id INT NOT NULL, INDEX IDX_F0BCBBE84D6CE55C (outillages_id), INDEX IDX_F0BCBBE8C9ECABC3 (prog_moyens_id), PRIMARY KEY(outillages_id, prog_moyens_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outillages_prog_moyens ADD CONSTRAINT FK_F0BCBBE84D6CE55C FOREIGN KEY (outillages_id) REFERENCES outillages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outillages_prog_moyens ADD CONSTRAINT FK_F0BCBBE8C9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE prog_moyens_outillages');
        $this->addSql('ALTER TABLE charg_fige ADD CONSTRAINT FK_B8CAC2A162BB7AEE FOREIGN KEY (programme_id) REFERENCES prog_moyens (id)');
        $this->addSql('CREATE INDEX IDX_B8CAC2A162BB7AEE ON charg_fige (programme_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX FK_D14CD140A0A1C920, ADD UNIQUE INDEX UNIQ_D14CD140A0A1C920 (programmes_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX FK_D14CD14049DEFD00, ADD UNIQUE INDEX UNIQ_D14CD14049DEFD00 (polym_plannif_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX FK_D14CD140A5AF1127, ADD UNIQUE INDEX UNIQ_D14CD140A5AF1127 (moyens_id)');
        $this->addSql('ALTER TABLE recurrance_polym DROP INDEX type_recurrance, ADD UNIQUE INDEX UNIQ_3A8E16BFA8E3DFCE (type_recurrance_id)');
        $this->addSql('ALTER TABLE recurrance_polym ADD CONSTRAINT FK_3A8E16BF47631EF9 FOREIGN KEY (num_planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE recurrance_polym ADD CONSTRAINT FK_3A8E16BFA8E3DFCE FOREIGN KEY (type_recurrance_id) REFERENCES type_recurrance (id)');
        $this->addSql('ALTER TABLE recurrance_polym RENAME INDEX num_planning TO UNIQ_3A8E16BF47631EF9');
        $this->addSql('ALTER TABLE type_recurrance CHANGE nbr_jour_cycle nbr_jour_cycle INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE prog_moyens_outillages (prog_moyens_id INT NOT NULL, outillages_id INT NOT NULL, INDEX IDX_EDC55E994D6CE55C (outillages_id), INDEX IDX_EDC55E99C9ECABC3 (prog_moyens_id), PRIMARY KEY(prog_moyens_id, outillages_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE prog_moyens_outillages ADD CONSTRAINT FK_EDC55E994D6CE55C FOREIGN KEY (outillages_id) REFERENCES outillages (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE prog_moyens_outillages ADD CONSTRAINT FK_EDC55E99C9ECABC3 FOREIGN KEY (prog_moyens_id) REFERENCES prog_moyens (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE outillages_prog_moyens');
        $this->addSql('ALTER TABLE charg_fige DROP FOREIGN KEY FK_B8CAC2A162BB7AEE');
        $this->addSql('DROP INDEX IDX_B8CAC2A162BB7AEE ON charg_fige');
        $this->addSql('ALTER TABLE polym_real DROP INDEX UNIQ_D14CD14049DEFD00, ADD INDEX FK_D14CD14049DEFD00 (polym_plannif_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX UNIQ_D14CD140A5AF1127, ADD INDEX FK_D14CD140A5AF1127 (moyens_id)');
        $this->addSql('ALTER TABLE polym_real DROP INDEX UNIQ_D14CD140A0A1C920, ADD INDEX FK_D14CD140A0A1C920 (programmes_id)');
        $this->addSql('ALTER TABLE recurrance_polym DROP INDEX UNIQ_3A8E16BFA8E3DFCE, ADD INDEX type_recurrance (type_recurrance_id)');
        $this->addSql('ALTER TABLE recurrance_polym DROP FOREIGN KEY FK_3A8E16BF47631EF9');
        $this->addSql('ALTER TABLE recurrance_polym DROP FOREIGN KEY FK_3A8E16BFA8E3DFCE');
        $this->addSql('ALTER TABLE recurrance_polym RENAME INDEX uniq_3a8e16bf47631ef9 TO num_planning');
        $this->addSql('ALTER TABLE type_recurrance CHANGE nbr_jour_cycle nbr_jour_cycle INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\'');
    }
}
