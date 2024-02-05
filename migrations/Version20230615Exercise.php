<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230615Exercise extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE exercise_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exercise (id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(20) NOT NULL, description VARCHAR(511) DEFAULT NULL, execution_technique VARCHAR(1023) DEFAULT NULL, execution_tips VARCHAR(511) DEFAULT NULL, slug VARCHAR(40) NOT NULL, difficulty SMALLINT DEFAULT NULL, views_number INT NOT NULL DEFAULT 0, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, image_filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX EXERCISE_UNIQUE_SLUG ON exercise (slug)');
        $this->addSql('CREATE INDEX EXERCISE_INDEX_AUTHOR_ID ON exercise (author_id)');
        $this->addSql('CREATE UNIQUE INDEX EXERCISE_UNIQUE_TITLE_AUTHOR_ID ON exercise (title, author_id)');
        $this->addSql('COMMENT ON COLUMN exercise.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT EXERCISE_FK_AUTHOR_ID FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE exercise_id_seq CASCADE');
        $this->addSql('ALTER TABLE exercise DROP CONSTRAINT EXERCISE_FK_AUTHOR_ID');
        $this->addSql('DROP TABLE exercise');
    }
}
