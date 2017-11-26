<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170610153057 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFA64C19C1');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP INDEX IDX_885DBAFA64C19C1 ON posts');
        $this->addSql('ALTER TABLE posts ADD text VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD dateInsert DATETIME DEFAULT NULL, ADD dateUpdate DATETIME DEFAULT NULL, DROP category, DROP slug, DROP summary, DROP content, DROP author_email, DROP published_at');
        $this->addSql('DROP INDEX UNIQ_1483A5E9F85E0677 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('ALTER TABLE users ADD role ENUM(\'ADMIN\',\'MEMBER\',\'GUEST\') NOT NULL DEFAULT \'MEMBER\', DROP roles, CHANGE name name VARCHAR(255) NOT NULL, CHANGE surname surname VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(60) NOT NULL, CHANGE is_active active TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci, slug VARCHAR(30) NOT NULL COLLATE utf8_unicode_ci, description VARCHAR(200) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, content LONGTEXT NOT NULL COLLATE utf8_unicode_ci, author_email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, published_at DATETIME NOT NULL, INDEX IDX_5F9E962A4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE posts ADD category INT NOT NULL, ADD slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD summary VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD content LONGTEXT NOT NULL COLLATE utf8_unicode_ci, ADD author_email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD published_at DATETIME NOT NULL, DROP text, DROP image, DROP dateInsert, DROP dateUpdate');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFA64C19C1 FOREIGN KEY (category) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_885DBAFA64C19C1 ON posts (category)');
        $this->addSql('ALTER TABLE users ADD roles LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', DROP role, CHANGE name name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, CHANGE surname surname VARCHAR(60) NOT NULL COLLATE utf8_unicode_ci, CHANGE password password VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE active is_active TINYINT(1) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }
}
