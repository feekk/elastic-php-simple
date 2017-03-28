<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class NotTerm{
    //protected $name = Type::ENOTMATCH;
    protected $mName;
    protected $mValue;
    protected $type = Type::EPHRASE;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        $m = new Term();
        return [
            Type::EBOOL => [
               Type::EMUSTNOT => $m->query($this->mName ,$this->mValue)
            ]
        ]; 
    }
}
