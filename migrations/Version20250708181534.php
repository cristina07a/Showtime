<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708181534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE band_user (band_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D6A5361249ABEB17 (band_id), INDEX IDX_D6A53612A76ED395 (user_id), PRIMARY KEY(band_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE code (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, is_available TINYINT(1) NOT NULL, percentage INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE code_festival (code_id INT NOT NULL, festival_id INT NOT NULL, INDEX IDX_FCFD8FA227DAFE17 (code_id), INDEX IDX_FCFD8FA28AEBAF57 (festival_id), PRIMARY KEY(code_id, festival_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, festivals_id INT NOT NULL, type VARCHAR(255) NOT NULL, price INT NOT NULL, INDEX IDX_97A0ADA312F492DD (festivals_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE band_user ADD CONSTRAINT FK_D6A5361249ABEB17 FOREIGN KEY (band_id) REFERENCES band (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE band_user ADD CONSTRAINT FK_D6A53612A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE code_festival ADD CONSTRAINT FK_FCFD8FA227DAFE17 FOREIGN KEY (code_id) REFERENCES code (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE code_festival ADD CONSTRAINT FK_FCFD8FA28AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA312F492DD FOREIGN KEY (festivals_id) REFERENCES festival (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE8AEBAF57
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E00CEDDE8AEBAF57 ON booking
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD tickets_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD paid_amount DOUBLE PRECISION NOT NULL, DROP festival_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8FDC0E9A FOREIGN KEY (tickets_id) REFERENCES ticket (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E00CEDDE8FDC0E9A ON booking (tickets_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDEA76ED395 ON booking (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE festival ADD photo_path VARCHAR(255) NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE8FDC0E9A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE band_user DROP FOREIGN KEY FK_D6A5361249ABEB17
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE band_user DROP FOREIGN KEY FK_D6A53612A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE code_festival DROP FOREIGN KEY FK_FCFD8FA227DAFE17
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE code_festival DROP FOREIGN KEY FK_FCFD8FA28AEBAF57
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA312F492DD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE band_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE code
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE code_festival
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE ticket
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_E00CEDDE8FDC0E9A ON booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E00CEDDEA76ED395 ON booking
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD festival_id INT NOT NULL, DROP tickets_id, DROP user_id, DROP paid_amount
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE8AEBAF57 ON booking (festival_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE festival DROP photo_path
        SQL);
    }
}
