<?php
include_once (_PS_MODULE_DIR_.'allinone_rewards/models/RewardsProductModel.php');
include_once (_PS_MODULE_DIR_.'allinone_rewards/models/RewardsModel.php');

class Search extends SearchCore{

    public static function find($id_lang, $expr, $page_number = 1, $page_size = 1, $order_by = 'position',
            $order_way = 'desc', $ajax = false, $use_cookie = true, Context $context = null)
    {
      if (!$context) {
        $context = Context::getContext();
      }
      $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

      // TODO : smart page management
      if ($page_number < 1) {
        $page_number = 1;
      }
      if ($page_size < 1) {
        $page_size = 1;
      }

      if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
        return false;
      }

      $intersect_array = array();
      $score_array = array();
      $words = explode(' ', Search::sanitize($expr, $id_lang, false, $context->language->iso_code));

      foreach ($words as $key => $word) {
        if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN')) {
          $word = str_replace(array('%', '_'), array('\\%', '\\_'), $word);
          $start_search = Configuration::get('PS_SEARCH_START') ? '%': '';
          $end_search = Configuration::get('PS_SEARCH_END') ? '': '%';

          $intersect_array[] = 'SELECT si.id_product
                                FROM '._DB_PREFIX_.'search_word sw
                                LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
                                WHERE sw.id_lang = '.(int)$id_lang.'
                                AND sw.id_shop = '.$context->shop->id.'
                                AND sw.word LIKE
                                '.($word[0] == '-'
                                ? ' \''.$start_search.pSQL(Tools::substr($word, 1, PS_SEARCH_MAX_WORD_LENGTH)).$end_search.'\''
                                : ' \''.$start_search.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).$end_search.'\''
                                );
                              
          if ($word[0] != '-') {
            $score_array[] = 'sw.word LIKE \''.$start_search.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).$end_search.'\'';
          }
        } else {
          unset($words[$key]);
        }
      }

      if (!count($words)) {
        return ($ajax ? array() : array('total' => 0, 'result' => array()));
      }

      $score = '';
        if (is_array($score_array) && !empty($score_array)) {
        $score = ',(
                    SELECT SUM(weight)
                    FROM '._DB_PREFIX_.'search_word sw
                    LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
                    WHERE sw.id_lang = '.(int)$id_lang.'
                      AND sw.id_shop = '.$context->shop->id.'
                      AND si.id_product = p.id_product
                      AND ('.implode(' OR ', $score_array).')
                    ) position';
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
          $groups = FrontController::getCurrentCustomerGroups();
          $sql_groups = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
        }

        $results = $db->executeS('
                    SELECT cp.`id_product`
                    FROM `'._DB_PREFIX_.'category_product` cp
                    '.(Group::isFeatureActive() ? 'INNER JOIN `'._DB_PREFIX_.'category_group` cg ON cp.`id_category` = cg.`id_category`' : '').'
                    INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
                    INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
                    '.Shop::addSqlAssociation('product', 'p', false).'
                    WHERE c.`active` = 1 AND p.product_parent = 1
                    AND product_shop.`active` = 1
                    AND product_shop.`visibility` IN ("both", "search")
                    AND product_shop.indexed = 1
                    '.$sql_groups, true, false);

        $eligible_products = array();
        foreach ($results as $row) {
          $eligible_products[] = $row['id_product'];
        }
        foreach ($intersect_array as $query) {
          $eligible_products2 = array();
          foreach ($db->executeS($query, true, false) as $row) {
            $eligible_products2[] = $row['id_product'];
          }

          $eligible_products = array_intersect($eligible_products, $eligible_products2);
          if (!count($eligible_products)) {
            return ($ajax ? array() : array('total' => 0, 'result' => array()));
          }
        }

        $eligible_products = array_unique($eligible_products);

        $product_pool = '';
        foreach ($eligible_products as $id_product) {
          if ($id_product) {
            $product_pool .= (int)$id_product.',';
          }
        }
        if (empty($product_pool)) {
          return ($ajax ? array() : array('total' => 0, 'result' => array()));
        }
        $product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));

        if ($ajax) {
          $sql = 'SELECT DISTINCT p.id_product, pl.name pname, cl.name cname,
                    cl.link_rewrite crewrite, pl.link_rewrite prewrite '.$score.'
                  FROM '._DB_PREFIX_.'product p
                  INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                    p.`id_product` = pl.`id_product`
                    AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
                  )
                  '.Shop::addSqlAssociation('product', 'p').'
                  INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (
                    product_shop.`id_category_default` = cl.`id_category`
                    AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
                  )
                  WHERE p.`id_product` '.$product_pool.'
                  ORDER BY position DESC LIMIT 10';
          return $db->executeS($sql, true, false);
        }

        if (strpos($order_by, '.') > 0) {
          $order_by = explode('.', $order_by);
          $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }
        $alias = '';
            if ($order_by == 'price') {
              $alias = 'product_shop.';
            } elseif (in_array($order_by, array('date_upd', 'date_add', 'id_product'))) {
              $alias = 'p.';
            }
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
                                    pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name` AS name_product, 
                                    image_shop.`id_image` id_image, il.`legend`, m.`name` manufacturer_name '.$score.',
                                    DATEDIFF(
                                            p.`date_add`,
                                            DATE_SUB(
                                                    "'.date('Y-m-d').' 00:00:00",
                                                    INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
                                            )
                                    ) > 0 new'.(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').'
                                    FROM '._DB_PREFIX_.'product p
                                    '.Shop::addSqlAssociation('product', 'p').'
                                    INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                                            p.`id_product` = pl.`id_product`
                                            AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
                                    )
                                    '.(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
                                    ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
                                    '.Product::sqlStock('p', 0).'
                                    LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                                    LEFT JOIN `ps_rewards_product` AS rp
                                            ON (rp.id_product = p.`id_product`)
                                    LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                                            ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
                                    LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
                                    WHERE p.`id_product` '.$product_pool.' AND p.product_parent = 1
                                    GROUP BY product_shop.id_product
                                    '.($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '').'
                                    LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;
            $lista=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);
            $result= array();
            foreach($lista as $x){
                $sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($context->customer->id);
                $sponsorships2=array_slice($sponsorships, 1, 15);
                $precio = RewardsProductModel::getProductReward($x['id_product'],$x['price'],1, $context->currency->id,0);
                $x['points']=round(RewardsModel::getRewardReadyForDisplay($precio, $context->currency->id)/(count($sponsorships2)+1));
                $x['pointsNl']=round(RewardsModel::getRewardReadyForDisplay($precio, $context->currency->id)/16);
                array_push($result,$x);
             }

            $sql = 'SELECT COUNT(*)
                                    FROM '._DB_PREFIX_.'product p
                                    '.Shop::addSqlAssociation('product', 'p').'
                                    INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                                            p.`id_product` = pl.`id_product`
                                            AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
                                    )
                                    LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
                                    WHERE p.`id_product` '.$product_pool.' AND p.product_parent = 1';
            $total = $db->getValue($sql, false);

            if (!$result) {
                $result_properties = false;
            } else {
                $result_properties = Product::getProductsProperties((int)$id_lang, $result);
            }

            return array('total' => $total,'result' => $result_properties);
        }
        
  public static function findApp($param, $option, $id_lang = 1){
    
    //error_log("\n\nSi entro a buscar y recibo: ".print_r("\nparam: ".$param."\nOption: ".$option, true),3,"/tmp/error.log");
    $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    
    //Entra value = palabra
    //Regresa manufacturer
    if ( $option == 1 ){
      $sql = 'SELECT DISTINCT
              '._DB_PREFIX_.'manufacturer.id_manufacturer AS m_id,
              '._DB_PREFIX_.'manufacturer.`name` AS m_name, 
              GROUP_CONCAT(DISTINCT product_child.price ORDER BY product_child.price SEPARATOR \',\') AS m_prices,
              MAX(product_child.reward) AS m_points 
              FROM
              '._DB_PREFIX_.'manufacturer_lang
              INNER JOIN '._DB_PREFIX_.'manufacturer ON '._DB_PREFIX_.'manufacturer.id_manufacturer = '._DB_PREFIX_.'manufacturer_lang.id_manufacturer
              INNER JOIN '._DB_PREFIX_.'product ON '._DB_PREFIX_.'product.id_manufacturer='._DB_PREFIX_.'manufacturer.id_manufacturer
              INNER JOIN '._DB_PREFIX_.'category_product ON '._DB_PREFIX_.'category_product.id_product='._DB_PREFIX_.'product.id_product
              INNER JOIN '._DB_PREFIX_.'category_lang ON '._DB_PREFIX_.'category_product.id_category = '._DB_PREFIX_.'category_lang.id_category
              INNER JOIN '._DB_PREFIX_.'product_lang ON '._DB_PREFIX_.'product_lang.id_product='._DB_PREFIX_.'product.id_product  
              INNER JOIN (
               SELECT '._DB_PREFIX_.'product.id_manufacturer, '._DB_PREFIX_.'product.price, (('._DB_PREFIX_.'product.price*('._DB_PREFIX_.'rewards_product.`value`/100)/25)) AS reward
               FROM '._DB_PREFIX_.'product
               INNER JOIN ps_rewards_product ON '._DB_PREFIX_.'rewards_product.id_product=ps_product.id_product 
               WHERE '._DB_PREFIX_.'product.product_parent=0  AND '._DB_PREFIX_.'product.active=1
              ) AS product_child ON product_child.id_manufacturer='._DB_PREFIX_.'manufacturer.id_manufacturer
              WHERE
              ('._DB_PREFIX_.'product.product_parent = 1 AND
              '._DB_PREFIX_.'manufacturer.active = 1 AND ('._DB_PREFIX_.'manufacturer.name LIKE \'%'.$param.'%\' 
              OR '._DB_PREFIX_.'manufacturer_lang.description LIKE \'%'.$param.'%\' 
              OR '._DB_PREFIX_.'manufacturer_lang.short_description LIKE \'%'.$param.'%\' 
              OR '._DB_PREFIX_.'manufacturer_lang.meta_title LIKE \'%'.$param.'%\' 
              OR '._DB_PREFIX_.'manufacturer_lang.meta_keywords LIKE \'%'.$param.'%\' 
              OR '._DB_PREFIX_.'manufacturer_lang.meta_description LIKE \'%'.$param.'%\'
              OR '._DB_PREFIX_.'category_lang.name LIKE \'%'.$param.'%\'
              OR '._DB_PREFIX_.'category_lang.description LIKE \'%'.$param.'%\'  
              OR '._DB_PREFIX_.'product_lang.name LIKE \'%'.$param.'%\'
              OR '._DB_PREFIX_.'product_lang.description LIKE \'%'.$param.'%\'
              OR '._DB_PREFIX_.'product_lang.meta_description LIKE \'%'.$param.'%\' ))
              GROUP BY '._DB_PREFIX_.'manufacturer.id_manufacturer';
//        error_log("\n\n\n\n\n*********************************\n Este es el query de busqueda: \n\n*********************************************\n\n".print_r($sql, true),3,"/tmp/error.log");
        $result = array();
        $result = $db->executeS($sql);
//        error_log("\n\n\n\n\n*\n Este es el result: \n\n*\n\n".print_r($result, true),3,"/tmp/error.log");
        $total = count($result);
//        error_log("\n\n\n\n\n*\n Este es el total: \n\n*\n\n".print_r($total, true),3,"/tmp/error.log");
        return array('total' => $total,'result' => $result);
    }
    //Entra value = manufacturer
    //Poductos padre con el valor maximo a ganar según hijos 
    else if ( $option == 2 ){
      $sql = 'SELECT 
                ip.id_manufacturer,
                pa.id_product AS id_parent,
                pl.name AS p_name,
                GROUP_CONCAT(DISTINCT ip.price ORDER BY ip.price SEPARATOR \',\') AS rango_precio,
                MAX((ip.price * ( rp.value / 100 ) ) / 25 ) AS points
              FROM '._DB_PREFIX_.'product_attribute AS pa 
              LEFT JOIN  '._DB_PREFIX_.'product AS ip ON ip.reference = pa.reference
              INNER JOIN '._DB_PREFIX_.'rewards_product AS rp ON rp.id_product = ip.id_product
              INNER JOIN '._DB_PREFIX_.'product_lang AS pl ON pl.id_product=pa.id_product
              INNER JOIN '._DB_PREFIX_.'manufacturer as m ON m.id_manufacturer=ip.id_manufacturer
              INNER JOIN '._DB_PREFIX_.'manufacturer_lang AS ml ON ml.id_manufacturer = m.id_manufacturer
              WHERE m.active=1 AND ip.active=1  AND ip.id_manufacturer = '.$param.'
              GROUP BY id_parent
              ORDER BY id_manufacturer, id_parent';
      
      $result = $db->executeS($sql);
//      error_log("\n\nEste es el result de opcion ".$option.":\n".print_r($result, true),3,"/tmp/error.log");
      return array('result' => $result);
    }
    //Entra value = id_padre
    //Productos hijo con su descripcion.
    else if ( $option == 3 ){
      $sql = 'SELECT 
                p.id_product AS c_id_product,
                p.reference AS c_reference,
                p.price AS c_price,
                p.price_shop AS c_price_shop, 
                rp.`value` AS c_value,
                pl.description AS instructions,
                pl.description_short AS terms
              FROM '._DB_PREFIX_.'product AS p
              INNER JOIN '._DB_PREFIX_.'product_attribute AS pa ON pa.reference = p.reference
              INNER JOIN '._DB_PREFIX_.'product_lang AS pl ON pl.id_product = pa.id_product
              INNER JOIN '._DB_PREFIX_.'rewards_product AS rp ON rp.id_product = p.id_product
              WHERE p.active = 1 and pa.id_product = '.$param.' AND pl.id_lang = '.$id_lang.'
              GROUP BY pa.id_product_attribute';
      
      $result = $db->executeS($sql);
      return array('result' => $result);
    }
  }
}
?>
