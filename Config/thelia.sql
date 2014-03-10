
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- order_product_partial_delivery
-- ---------------------------------------------------------------------

-- Create the table with previous order products,
-- see mysql document to get more information about this syntax:
-- http://dev.mysql.com/doc/refman/5.0/en/create-table-select.html
CREATE TABLE IF NOT EXISTS `order_product_partial_delivery`
(
    `id` INTEGER NOT NULL,
    `sent_quantity` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_order_product_partial_delivery_order_product_id`
        FOREIGN KEY (`id`)
        REFERENCES `order_product` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB SELECT id, 0 AS `sent_quantity` FROM `order_product`;



-- ---------------------------------------------------------------------
-- Mail templates for partial delivery
-- ---------------------------------------------------------------------
-- First, delete existing entries
SET @var := 0;
SELECT @var := `id` FROM `message` WHERE name="partial_delivery_send_info";
DELETE FROM `message` WHERE `id`=@var;
-- Try if ON DELETE constraint isn't set
DELETE FROM `message_i18n` WHERE `id`=@var;

-- Then add new entries
SELECT @max := MAX(`id`) FROM `message`;
SET @max := @max+1;
-- insert message
INSERT INTO `message` (`id`, `name`, `secured`) VALUES
  (@max,
   'partial_delivery_send_info',
   '0'
  );

-- and template fr_FR
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max, 'fr_FR', 'mail livraison partielle', 'Suivi de votre commande: {$order_ref}', '{loop type="customer" name="customer.order" current="false" id="$customer_id" backend_context="1"}
{$LASTNAME} {$FIRSTNAME},
{/loop}
Nous vous remercions de votre commande sur notre site {config key="store_name"}
Un colis concernant une partie votre commande {$order_ref} du {format_date date=$order_date} a quitté nos entrepôts, voici les produits que vous allez recevoir:
{for $i=0 to $nbproducts-1}
* {$products.{$i}.title} - Quantité: {$products.{$i}.quantity}
{/for}
Nous restons à votre disposition pour toute information complémentaire.
Cordialement','');


# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
