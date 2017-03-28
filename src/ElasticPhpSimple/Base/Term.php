<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class Term{
    protected $name = Type::ETERM;
    protected $mName ;
    protected $mValue;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        return [
            $this->name => [
                $this->mName => $this->mValue
            ]
        ]; 
    }
}
