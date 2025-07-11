<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708193023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE8FDC0E9A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_E00CEDDE8FDC0E9A ON booking
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking CHANGE tickets_id ticket_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E00CEDDE700047D2 ON booking (ticket_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE700047D2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_E00CEDDE700047D2 ON booking
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking CHANGE ticket_id tickets_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE8FDC0E9A FOREIGN KEY (tickets_id) REFERENCES ticket (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E00CEDDE8FDC0E9A ON booking (tickets_id)
        SQL);
    }
}
