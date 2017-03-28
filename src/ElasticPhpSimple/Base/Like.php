<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class Like{
    protected $name = Type::EWILDCARD;
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
                $this->mName => str_replace('%' ,'*' ,$this->mValue)
            ]
        ]; 
    }
}
