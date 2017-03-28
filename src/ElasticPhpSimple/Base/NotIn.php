<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class NotIn{
    protected $mName ;
    protected $mValArr;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValArr = $value;
        return $this->_build(); 
    }

    protected function _build(){
        $in = new In();
        $ret = $in->query($this->mName ,$this->mValArr);
        
        return ( is_null($ret) ? $ret :[
            Type::EBOOL => [
                Type::EMUSTNOT => $ret
            ]
        ]);   
    }
}
