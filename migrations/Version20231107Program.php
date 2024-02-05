<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231107Program extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE program_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE program_exercise_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE program (id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(40) NOT NULL, description VARCHAR(511) DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX PROGRAM_INDEX_AUTHOR_ID ON program (author_id)');
        $this->addSql('CREATE UNIQUE INDEX PROGRAM_UNIQUE_TITLE_AUTHOR_ID ON program (title, author_id)');
        $this->addSql('COMMENT ON COLUMN program.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE program_exercise (id INT NOT NULL, program_id INT NOT NULL, exercise_id INT NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX PROGRAM_EXERCISE_INDEX_PROGRAM_ID ON program_exercise (program_id)');
        $this->addSql('CREATE INDEX PROGRAM_EXERCISE_INDEX_EXERCISE_ID ON program_exercise (exercise_id)');
        $this->addSql('CREATE UNIQUE INDEX PROGRAM_EXERCISE_UNIQUE_PROGRAM_ID_EXERCISE_ID_POSITION ON program_exercise (program_id, exercise_id, position)');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT PROGRAM_FK_AUTHOR_ID FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_exercise ADD CONSTRAINT PROGRAM_EXERCISE_FK_PROGRAM_ID FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_exercise ADD CONSTRAINT PROGRAM_EXERCISE_FK_EXERCISE_ID FOREIGN KEY (exercise_id) REFERENCES exercise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE program_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE program_exercise_id_seq CASCADE');
        $this->addSql('ALTER TABLE program DROP CONSTRAINT PROGRAM_FK_AUTHOR_ID');
        $this->addSql('ALTER TABLE program_exercise DROP CONSTRAINT PROGRAM_EXERCISE_FK_PROGRAM_ID');
        $this->addSql('ALTER TABLE program_exercise DROP CONSTRAINT PROGRAM_EXERCISE_FK_EXERCISE_ID');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE program_exercise');
    }
}
