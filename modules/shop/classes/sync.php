<?php 

class sync {
    
    
    private $client;
    private $config;
    
    private $guid = array(
        'price' =>  '5634b535-b2f4-11e5-8642-08606e834058',
        'price2' => '5634b536-b2f4-11e5-8642-08606e834058',
        'price3' => '5634b534-b2f4-11e5-8642-08606e834058',
        'price4' => '5634b537-b2f4-11e5-8642-08606e834058',
        'price5' => '5634b532-b2f4-11e5-8642-08606e834058'
    );
    
    
    public function __construct() {
        $this->config = core::$instance->CONFIG['1cServices'];
        $options = array(
            'login'=> $this->config['price']['login'],
            'password' => $this->config['price']['password']
        );
        
        $this->client = new SoapClient($this->config['price']['url'], $options);
    }
    
    
    public function getProductByArticle($article) {
        $response = $this->client->{$this->config['price']['function']}(array('arrCodes'=>array($article)));
        if($response instanceof stdClass) {
            if(!empty($response) && !empty($response->return && !empty($response->return->Товар))) {
    
                $return = array(
                    'article' => $response->return->Товар->Артикул,
                    'title' => $response->return->Товар->Наименование,
                    'stock' => $response->return->Товар->Остаток,
                    'price' => array(
                        'price' => $this->getPrice('price', $response->return->Товар->Цены->ЗаписьЦены),
                        'price2' => $this->getPrice('price2', $response->return->Товар->Цены->ЗаписьЦены),
                        'price3' => $this->getPrice('price3', $response->return->Товар->Цены->ЗаписьЦены),
                        'price4' => $this->getPrice('price4', $response->return->Товар->Цены->ЗаписьЦены),
                        'price5' => $this->getPrice('price5', $response->return->Товар->Цены->ЗаписьЦены),
                     )
                );
                
                return $return;
            }
        }
    }
    
    
    private function getPrice($field, $price){
        
        foreach($price as $item) {
            if($item->GUID == $this->guid[$field]) {
                return $item->Цена;
            }
        }
        
        return 0;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
    
    
    
    
    private $db = false;
    private $clientId;
    private $shopId;
    private $config;
    
    private $tables = array(
        'articles'=>'SC84',
        'prices'=>'SC319',
        'constant'=>'_1SCONST'  
    );
    
    private $colums = array(
        'articles' => array(
            'article'=>'SP85',
            'code'=>'CODE',
            'objid'=>'ID',
            'title'=>'DESCR',
            'fullname'=>'SP101',
            'package'=>'SP8820',
            'weight'=>'SP103',
            'unit'=>'SP94'  
        )
    );
    
    private $priceCompare = array(
        'price'=>'7', 
        'price2'=>'8',
        'price3'=>'2',
        'price4'=>'A',
        'price5'=>'3'
    );
    
    public function __construct($clietnId, $shopId, $config) {
        $this->clientId = $clietnId;
        $this->shopId = $shopId;
        $this->config = $config;
    }
    
    public function updateAll() {
        $db = core::$instance->db;
        $sql = "SELECT product_id, article  FROM tp_product WHERE parent_id != 0";
        $db->query($sql);
        $db->get_rows();
        $list = $db->rows;
        
        $limit = 0;
        $currentIteration = 0;
        
        foreach($list as $item) {
            if(empty($item['article'])) continue;
            $currentIteration++;
                        
            $data = $this->getProductByArticle($item['article']);
               
            $product = array(
                'client_id'=>$this->clientId,
                'shop_id'=>$this->shopId,
                'product_id'=>$item['product_id'],
                'pack_size'=>floatval($data['package']),
                'price_type'=>(floatval($data['package']) > 0)? 2 : 1,
                'min_order'=>(floatval($data['package']) > 0)? 1 : 2
            );
            
            foreach($this->priceCompare as $field=>$dataKey) {
                $product[$field] = floatval($data['prices'][$dataKey]['value']);
            }
  
            $db->autoupdate()->table('tp_product')->data(array($product))->primary('client_id', 'shop_id', 'product_id');
            $db->execute();
            $db->debug();
            
            if($limit > 0 && $limit <= $currentIteration) {
                break;
            }
        }
        
       //echo count($list);
    }
    
    public function connectToDb() {
        $this->db = mssql_connect($this->config['ip'], $this->config['login'], $this->config['pass']);
       if(!$this->db) {
           echo 'Cold not connect to MSSQL Database!'; echo "\n: " . mssql_get_last_message();  die();
       }
        if(!mssql_select_db($this->config['dbname'], $this->db)){
            echo 'Db not select!';
            die();
        }
    }
    
    public function getCodeByArticle($article) {
        if(!$this->db) $this->connectToDb();
        $data = $this->getCatalogItemObjectbyArticle($article);
        if(empty($data)) return;
        
        return $data['objid'];
    }
    
    public function getProductByArticle($article) {
        return $this->getCatalogItemObjectbyArticle($article);
    }
    

    public function getRemainByObjId() {
        
    }
    
    
    
    private function getUnitByObjId($objId) {
        if(!$this->db) $this->connectToDb();
        $sql = "SELECT a2.DESCR FROM SC75 AS a1 LEFT JOIN SC41 as a2 ON a2.ID = a1.SP79 LEFT JOIN SC75 AS a3 ON a1.ID = a3.PARENTEXT WHERE a3.ID = '{$objId}'";
        $result = mssql_query($sql, $this->db);
        $object = mssql_fetch_assoc($result);
        if(empty($object)) return null;
        
        return mb_convert_encoding($object['DESCR'], 'utf-8', 'cp1251');
    }
    
    private function getCatalogItemObjectbyArticle($article) {
        if(!$this->db) $this->connectToDb();
        $sql = "SELECT * FROM {$this->tables['articles']} WHERE {$this->colums['articles']['article']} = '{$article}'";
        $result = mssql_query($sql, $this->db);
        $object = mssql_fetch_assoc($result);
        if(empty($object)) return null;
        
        $data = array();
        foreach($this->colums['articles'] as $alias=>$name) {
            $data[$alias] = mb_convert_encoding($object[$name], 'utf-8', 'cp1251');
        }
        
        $data['unit_name'] = $this->getUnitByObjId($data['unit']);
        $data['prices'] = $this->getPriceByObjId($data['objid']);
        return $data;
    }
    
    private function getPriceByObjId($objId) {
        if(!$this->db) $this->connectToDb();
        $sql = "SELECT a1.ID, a1.DESCR, a1.SP221, a1.SP225 FROM SC219 as a1 WHERE a1.SP224 = 1";
        $result = mssql_query($sql, $this->db);
        $calc = array();
        while($row = mssql_fetch_assoc($result)) { 
            $row['DESCR'] = mb_convert_encoding($row['DESCR'], 'utf-8', 'cp1251');
            $calc[] = $row;
        } 
        
        $sql = "
        
        
        SELECT SC319.ID, SC219.ID as _id, SC219.DESCR, SC219.SP224 as calculated FROM SC319  LEFT JOIN SC219 ON SC219.ID = SC319.SP327 WHERE 
        SC319.PARENTEXT = '{$objId}'";
        $result = mssql_query($sql, $this->db);
        $prices = array();
        $priceById = array();
        
        while($row = mssql_fetch_assoc($result)) {
            $row['DESCR'] = mb_convert_encoding($row['DESCR'], 'utf-8', 'cp1251');
            $row['value'] = round(floatval($this->getItemFromConst($row['ID'])),2);
            $row['ID'] = trim($row['_id']);
            $priceById[$row['ID']] = $row['value'];
            unset($row['_id']);
            $prices[$row['ID']] = $row;
        }
        
        foreach($calc as $item) {
            $prices[trim($item['ID'])] = array(
                'DESCR'=>$item['DESCR'],
                'ID'=>trim($item['ID']),
                'calculated'=>1,
                'value'=>$this->calcPrice($priceById[trim($item['SP225'])], $item['SP221'])
            );
        }

        
        return $prices;
    }
    
    private function getItemFromConst($objId) {
        if(!$this->db) $this->connectToDb();
        $sql = "SELECT VALUE FROM {$this->tables['constant']} WHERE OBJID = '{$objId}' AND ID = 324 ORDER BY DATE DESC ";
        $result = mssql_query($sql, $this->db);
        $price = mssql_fetch_assoc($result)['VALUE'];
        
        return $price;
    }
    
    private function calcPrice($basePrice, $modifier) {
        $modifier = floatval($modifier);
        if($modifier < 0) {
            $price = $basePrice - ($basePrice / 100 * $modifier);
        } else {
            $price = $basePrice + ($basePrice / 100 * $modifier);
        }
        
        return round($price, 2);
    }*/
}



?>