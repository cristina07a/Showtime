<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250708185313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA312F492DD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_97A0ADA312F492DD ON ticket
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE festivals_id festival_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA38AEBAF57 FOREIGN KEY (festival_id) REFERENCES festival (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_97A0ADA38AEBAF57 ON ticket (festival_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA38AEBAF57
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_97A0ADA38AEBAF57 ON ticket
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket CHANGE price price INT NOT NULL, CHANGE festival_id festivals_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA312F492DD FOREIGN KEY (festivals_id) REFERENCES festival (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_97A0ADA312F492DD ON ticket (festivals_id)
        SQL);
    }
}
