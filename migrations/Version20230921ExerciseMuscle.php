<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230921ExerciseMuscle extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE exercise_muscle_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE exercise_muscle (id INT NOT NULL, exercise_id INT NOT NULL, muscle SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX EXERCISE_MUSCLE_INDEX_EXERCISE_ID ON exercise_muscle (exercise_id)');
        $this->addSql('CREATE INDEX EXERCISE_MUSCLE_INDEX_MUSCLE ON exercise_muscle (muscle)');
        $this->addSql('CREATE UNIQUE INDEX EXERCISE_MUSCLE_UNIQUE_EXERCISE_ID_MUSCLE ON exercise_muscle (exercise_id, muscle)');
        $this->addSql('ALTER TABLE exercise_muscle ADD CONSTRAINT EXERCISE_MUSCLE_FK_EXERCISE_ID FOREIGN KEY (exercise_id) REFERENCES exercise (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN "exercise_muscle".muscle IS \'The muscles for the exercise are stored as an integer, see the values in App\\Shared\\Enum\\Muscle\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE exercise_muscle_id_seq CASCADE');
        $this->addSql('ALTER TABLE exercise_muscle DROP CONSTRAINT EXERCISE_MUSCLE_FK_EXERCISE_ID');
        $this->addSql('DROP TABLE exercise_muscle');
    }
}
