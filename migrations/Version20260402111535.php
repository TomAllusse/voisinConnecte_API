<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260402111535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcements ALTER id_user DROP NOT NULL');
        $this->addSql('ALTER TABLE announcements ALTER id_category DROP NOT NULL');
        $this->addSql('ALTER TABLE announcements ADD CONSTRAINT FK_F422A9D6B3CA4B FOREIGN KEY (id_user) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE announcements ADD CONSTRAINT FK_F422A9D5697F554 FOREIGN KEY (id_category) REFERENCES categories (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_F422A9D6B3CA4B ON announcements (id_user)');
        $this->addSql('CREATE INDEX IDX_F422A9D5697F554 ON announcements (id_category)');
        $this->addSql('ALTER TABLE refresh_tokens ALTER id_user DROP NOT NULL');
        $this->addSql('ALTER TABLE refresh_tokens ADD CONSTRAINT FK_9BACE7E16B3CA4B FOREIGN KEY (id_user) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_9BACE7E16B3CA4B ON refresh_tokens (id_user)');
        $this->addSql('ALTER TABLE reports ALTER id_announcement DROP NOT NULL');
        $this->addSql('ALTER TABLE reports ALTER id_reporter DROP NOT NULL');
        $this->addSql('ALTER TABLE reports ALTER resolved_by DROP NOT NULL');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7456E78AC6E FOREIGN KEY (id_announcement) REFERENCES announcements (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745260D0C67 FOREIGN KEY (id_reporter) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA74557EB21F9 FOREIGN KEY (resolved_by) REFERENCES users (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_F11FA7456E78AC6E ON reports (id_announcement)');
        $this->addSql('CREATE INDEX IDX_F11FA745260D0C67 ON reports (id_reporter)');
        $this->addSql('CREATE INDEX IDX_F11FA74557EB21F9 ON reports (resolved_by)');
        $this->addSql('ALTER TABLE responses ALTER id_announcement DROP NOT NULL');
        $this->addSql('ALTER TABLE responses ALTER id_responder DROP NOT NULL');
        $this->addSql('ALTER TABLE responses ADD CONSTRAINT FK_315F9F946E78AC6E FOREIGN KEY (id_announcement) REFERENCES announcements (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE responses ADD CONSTRAINT FK_315F9F94DF04A6D0 FOREIGN KEY (id_responder) REFERENCES categories (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_315F9F946E78AC6E ON responses (id_announcement)');
        $this->addSql('CREATE INDEX IDX_315F9F94DF04A6D0 ON responses (id_responder)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcements DROP CONSTRAINT FK_F422A9D6B3CA4B');
        $this->addSql('ALTER TABLE announcements DROP CONSTRAINT FK_F422A9D5697F554');
        $this->addSql('DROP INDEX IDX_F422A9D6B3CA4B');
        $this->addSql('DROP INDEX IDX_F422A9D5697F554');
        $this->addSql('ALTER TABLE announcements ALTER id_user SET NOT NULL');
        $this->addSql('ALTER TABLE announcements ALTER id_category SET NOT NULL');
        $this->addSql('ALTER TABLE refresh_tokens DROP CONSTRAINT FK_9BACE7E16B3CA4B');
        $this->addSql('DROP INDEX IDX_9BACE7E16B3CA4B');
        $this->addSql('ALTER TABLE refresh_tokens ALTER id_user SET NOT NULL');
        $this->addSql('ALTER TABLE reports DROP CONSTRAINT FK_F11FA7456E78AC6E');
        $this->addSql('ALTER TABLE reports DROP CONSTRAINT FK_F11FA745260D0C67');
        $this->addSql('ALTER TABLE reports DROP CONSTRAINT FK_F11FA74557EB21F9');
        $this->addSql('DROP INDEX IDX_F11FA7456E78AC6E');
        $this->addSql('DROP INDEX IDX_F11FA745260D0C67');
        $this->addSql('DROP INDEX IDX_F11FA74557EB21F9');
        $this->addSql('ALTER TABLE reports ALTER id_announcement SET NOT NULL');
        $this->addSql('ALTER TABLE reports ALTER id_reporter SET NOT NULL');
        $this->addSql('ALTER TABLE reports ALTER resolved_by SET NOT NULL');
        $this->addSql('ALTER TABLE responses DROP CONSTRAINT FK_315F9F946E78AC6E');
        $this->addSql('ALTER TABLE responses DROP CONSTRAINT FK_315F9F94DF04A6D0');
        $this->addSql('DROP INDEX IDX_315F9F946E78AC6E');
        $this->addSql('DROP INDEX IDX_315F9F94DF04A6D0');
        $this->addSql('ALTER TABLE responses ALTER id_announcement SET NOT NULL');
        $this->addSql('ALTER TABLE responses ALTER id_responder SET NOT NULL');
    }
}
