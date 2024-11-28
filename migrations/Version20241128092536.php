<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241128092536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pupil_session (pupil_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_A1C028DBD2FD11 (pupil_id), INDEX IDX_A1C028DB613FECDF (session_id), PRIMARY KEY(pupil_id, session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pupil_session ADD CONSTRAINT FK_A1C028DBD2FD11 FOREIGN KEY (pupil_id) REFERENCES pupil (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pupil_session ADD CONSTRAINT FK_A1C028DB613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pupil_session DROP FOREIGN KEY FK_A1C028DBD2FD11');
        $this->addSql('ALTER TABLE pupil_session DROP FOREIGN KEY FK_A1C028DB613FECDF');
        $this->addSql('DROP TABLE pupil_session');
    }
}
