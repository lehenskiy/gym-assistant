<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230513UserWeightAndProfileColumns extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE user_weight_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_weight (id INT NOT NULL, user_id INT NOT NULL, weight SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX USER_WEIGHT_INDEX_USER_ID ON user_weight (user_id)');
        $this->addSql('COMMENT ON COLUMN user_weight.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_weight ADD CONSTRAINT USER_WEIGHT_FK_USER_ID FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // safe as of PostgreSQL 11 (https://www.postgresql.org/docs/current/ddl-alter.html)
        $this->addSql('ALTER TABLE "user" ADD public BOOLEAN NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE "user" ADD gender SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD birth_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD height SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD goal_weight SMALLINT DEFAULT NULL');

        // comments
        $this->addSql('COMMENT ON TABLE user_weight IS \'weights are stored as kg*10 to prevent use of decimal type\'');
        $this->addSql('COMMENT ON COLUMN "user".gender IS \'1 - Male, 2 - Female (part of ISO 5218)\'');
        $this->addSql('COMMENT ON COLUMN "user".birth_date IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE user_weight_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_weight DROP CONSTRAINT USER_WEIGHT_FK_USER_ID');
        $this->addSql('DROP TABLE user_weight');
        $this->addSql('ALTER TABLE "user" DROP public');
        $this->addSql('ALTER TABLE "user" DROP gender');
        $this->addSql('ALTER TABLE "user" DROP birth_date');
        $this->addSql('ALTER TABLE "user" DROP height');
        $this->addSql('ALTER TABLE "user" DROP goal_weight');
    }
}
