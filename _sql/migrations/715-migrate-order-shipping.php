<?php

class Migrations_Migration715 extends Shopware\Components\Migrations\AbstractMigration
{
    /**
     * @param string $modus
     * @return void
     */
    public function up($modus)
    {
        if ($modus == self::MODUS_INSTALL) {
            return;
        }

        $sql = <<<SQL
INSERT IGNORE INTO s_user_addresses_migration (original_type, original_id, user_id, company, department, salutation, firstname, lastname, street, zipcode, city, additional_address_line1, additional_address_line2, country_id, state_id, checksum)
(
  SELECT
    's_order_shippingaddress' as original_type,
    s_order_shippingaddress.id as original_id,
    userID, company, department, s_order_shippingaddress.salutation, s_order_shippingaddress.firstname, s_order_shippingaddress.lastname, street, zipcode, city, additional_address_line1, additional_address_line2, countryID, IF(stateID = 0, NULL, stateID),
    MD5(CONCAT_WS('', userID, company, department, s_order_shippingaddress.salutation, s_order_shippingaddress.firstname, s_order_shippingaddress.lastname, street, zipcode, city, additional_address_line1, additional_address_line2, countryID, IF(stateID = 0, NULL, stateID)))
  FROM s_order_shippingaddress
  INNER JOIN s_user ON s_order_shippingaddress.userID = s_user.id
  INNER JOIN s_core_countries ON s_order_shippingaddress.countryID = s_core_countries.id
)
SQL;
        $this->addSql($sql);
    }
}
