<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class In{
    protected $mName ;
    protected $mValArr;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValArr = $value;
        return $this->_build(); 
    }

    protected function _build(){
        $match = [];
        if(!empty($this->mValArr) && is_array($this->mValArr)){
            foreach($this->mValArr as $_k =>$val){
                $m = new Match();
                $match[] = $m->query($this->mName ,$val); 
            }  
            return [
                Type::EBOOL => [
                    Type::ESHOULD => $match
                ]
            ];  
        }
        return null;
    }
}
