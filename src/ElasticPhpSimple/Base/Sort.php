<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class Sort{
    protected $orderBy; 
    protected $order; 

    public function order($orderBy ,$order){
        $this->orderBy = $orderBy;     
        $this->order = $order;     
        return $this->_build(); 
    }
    protected function _build(){
        return [
            $this->orderBy => [
                Type::EORDER => $this->order    
            ]
        ]; 
    }
}
