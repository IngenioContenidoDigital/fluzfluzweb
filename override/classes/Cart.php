<?php

class Cart extends CartCore
{
    public function removeAllCartRules()
    {
        $sql = "DELETE FROM "._DB_PREFIX_."cart_cart_rule
                WHERE id_cart = ".(int)$this->id;
        Db::getInstance()->execute($sql);
    }

}
