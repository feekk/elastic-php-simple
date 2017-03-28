<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class NotMatch{
    protected $name = Type::ENOTMATCH;
    protected $mName;
    protected $mValue;
    protected $type = Type::EPHRASE;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        $m = new Match();
        return [
            Type::EBOOL => [
               Type::EMUSTNOT => $m->query($this->mName ,$this->mValue)
            ]
        ]; 
    }
}
