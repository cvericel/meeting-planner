<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200218132239 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE guest_without_account (id INT AUTO_INCREMENT NOT NULL, meeting_guest_id INT NOT NULL, invited_at DATETIME NOT NULL, valid TINYINT(1) NOT NULL, role VARCHAR(40) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_71FFC7E39DB8B8D (meeting_guest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guest_with_account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, meeting_guest_id INT NOT NULL, invited_at DATETIME NOT NULL, valid TINYINT(1) NOT NULL, role VARCHAR(40) NOT NULL, INDEX IDX_77EABFF0A76ED395 (user_id), UNIQUE INDEX UNIQ_77EABFF09DB8B8D (meeting_guest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guest_without_account ADD CONSTRAINT FK_71FFC7E39DB8B8D FOREIGN KEY (meeting_guest_id) REFERENCES meeting_guest (id)');
        $this->addSql('ALTER TABLE guest_with_account ADD CONSTRAINT FK_77EABFF0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE guest_with_account ADD CONSTRAINT FK_77EABFF09DB8B8D FOREIGN KEY (meeting_guest_id) REFERENCES meeting_guest (id)');
        $this->addSql('ALTER TABLE meeting_guest DROP FOREIGN KEY FK_C486D835A76ED395');
        $this->addSql('DROP INDEX IDX_C486D835A76ED395 ON meeting_guest');
        $this->addSql('ALTER TABLE meeting_guest DROP user_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE guest_without_account');
        $this->addSql('DROP TABLE guest_with_account');
        $this->addSql('ALTER TABLE meeting_guest ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE meeting_guest ADD CONSTRAINT FK_C486D835A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C486D835A76ED395 ON meeting_guest (user_id)');
    }
}
