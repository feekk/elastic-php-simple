<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class Match{
    protected $name = Type::EMATCH;
    protected $mName ;
    protected $mValue;
    protected $type = Type::EPHRASE;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        return [
            $this->name => [
                $this->mName =>[
                    Type::EQUERY => $this->mValue,
                    Type::ETYPE => $this->type 
                ]
            ]
        ]; 
    }
}
