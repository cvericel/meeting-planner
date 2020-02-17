<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217210317 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meeting ADD chosen_date_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139426380F0 FOREIGN KEY (chosen_date_id) REFERENCES meeting_date (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F515E139426380F0 ON meeting (chosen_date_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139426380F0');
        $this->addSql('DROP INDEX UNIQ_F515E139426380F0 ON meeting');
        $this->addSql('ALTER TABLE meeting DROP chosen_date_id');
    }
}
