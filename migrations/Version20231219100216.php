<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219100216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, address_category_id INT NOT NULL, user_address_id INT NOT NULL, phone_number VARCHAR(50) NOT NULL, address VARCHAR(255) NOT NULL, extended_address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) NOT NULL, city VARCHAR(50) NOT NULL, country VARCHAR(50) NOT NULL, INDEX IDX_D4E6F81589BE29 (address_category_id), INDEX IDX_D4E6F8152D06999 (user_address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, cart_user_id INT NOT NULL, quantity INT NOT NULL, UNIQUE INDEX UNIQ_BA388B75CB41B92 (cart_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, object VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, email VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, image_id INT NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', url VARCHAR(255) DEFAULT NULL, INDEX IDX_C53D045F3DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE line_cart (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, line_cart_product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_80EE7B6E1AD5CDBF (cart_id), INDEX IDX_80EE7B6E8B0B8AD0 (line_cart_product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE line_product (id INT AUTO_INCREMENT NOT NULL, line_product_order_id INT NOT NULL, product_line_id INT NOT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, INDEX IDX_66EE85CA185F1B3D (line_product_order_id), INDEX IDX_66EE85CA9CA26EF2 (product_line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_subscriber (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_401562C3E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, invoice_address_id INT DEFAULT NULL, delivery_address_id INT DEFAULT NULL, order_user_id INT NOT NULL, reference VARCHAR(255) NOT NULL, total_amount NUMERIC(10, 0) NOT NULL, order_date DATETIME NOT NULL, payment_status VARCHAR(255) NOT NULL, delivery_status VARCHAR(255) NOT NULL, INDEX IDX_F5299398C6BDFEB (invoice_address_id), INDEX IDX_F5299398EBF23851 (delivery_address_id), INDEX IDX_F529939851147ADE (order_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, product_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, taxe NUMERIC(10, 2) NOT NULL, description VARCHAR(255) NOT NULL, detailed_description LONGTEXT NOT NULL, mesurement VARCHAR(255) NOT NULL, stock VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D34A04AD14959723 (product_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_tag (product_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_E3A6E39C4584665A (product_id), INDEX IDX_E3A6E39CBAD26311 (tag_id), PRIMARY KEY(product_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE returned_product (id INT AUTO_INCREMENT NOT NULL, order_returned_product_id INT NOT NULL, product_returned_id INT NOT NULL, returned_date DATETIME NOT NULL, returned_object VARCHAR(255) NOT NULL, returned_status VARCHAR(255) NOT NULL, INDEX IDX_AF6E52FE34DAD0BE (order_returned_product_id), INDEX IDX_AF6E52FE2A482DBB (product_returned_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, product_review_id INT NOT NULL, review_user_id INT NOT NULL, rating INT NOT NULL, comment LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_794381C6508E2016 (product_review_id), INDEX IDX_794381C63C806762 (review_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, tag_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, type_name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_8CDE5729727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, account_status VARCHAR(50) DEFAULT NULL, reset_token VARCHAR(64) DEFAULT NULL, is_email_confirmed TINYINT(1) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81589BE29 FOREIGN KEY (address_category_id) REFERENCES address_category (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F8152D06999 FOREIGN KEY (user_address_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B75CB41B92 FOREIGN KEY (cart_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F3DA5256D FOREIGN KEY (image_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE line_cart ADD CONSTRAINT FK_80EE7B6E1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE line_cart ADD CONSTRAINT FK_80EE7B6E8B0B8AD0 FOREIGN KEY (line_cart_product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE line_product ADD CONSTRAINT FK_66EE85CA185F1B3D FOREIGN KEY (line_product_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE line_product ADD CONSTRAINT FK_66EE85CA9CA26EF2 FOREIGN KEY (product_line_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C6BDFEB FOREIGN KEY (invoice_address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939851147ADE FOREIGN KEY (order_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD14959723 FOREIGN KEY (product_type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_tag ADD CONSTRAINT FK_E3A6E39CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE returned_product ADD CONSTRAINT FK_AF6E52FE34DAD0BE FOREIGN KEY (order_returned_product_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE returned_product ADD CONSTRAINT FK_AF6E52FE2A482DBB FOREIGN KEY (product_returned_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6508E2016 FOREIGN KEY (product_review_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C63C806762 FOREIGN KEY (review_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE5729727ACA70 FOREIGN KEY (parent_id) REFERENCES type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81589BE29');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F8152D06999');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B75CB41B92');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F3DA5256D');
        $this->addSql('ALTER TABLE line_cart DROP FOREIGN KEY FK_80EE7B6E1AD5CDBF');
        $this->addSql('ALTER TABLE line_cart DROP FOREIGN KEY FK_80EE7B6E8B0B8AD0');
        $this->addSql('ALTER TABLE line_product DROP FOREIGN KEY FK_66EE85CA185F1B3D');
        $this->addSql('ALTER TABLE line_product DROP FOREIGN KEY FK_66EE85CA9CA26EF2');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C6BDFEB');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398EBF23851');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939851147ADE');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD14959723');
        $this->addSql('ALTER TABLE product_tag DROP FOREIGN KEY FK_E3A6E39C4584665A');
        $this->addSql('ALTER TABLE product_tag DROP FOREIGN KEY FK_E3A6E39CBAD26311');
        $this->addSql('ALTER TABLE returned_product DROP FOREIGN KEY FK_AF6E52FE34DAD0BE');
        $this->addSql('ALTER TABLE returned_product DROP FOREIGN KEY FK_AF6E52FE2A482DBB');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6508E2016');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C63C806762');
        $this->addSql('ALTER TABLE type DROP FOREIGN KEY FK_8CDE5729727ACA70');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE address_category');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE line_cart');
        $this->addSql('DROP TABLE line_product');
        $this->addSql('DROP TABLE newsletter_subscriber');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_tag');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE returned_product');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
