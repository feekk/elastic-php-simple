<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class Missing{
    protected $name = Type::EMISSING;
    protected $mName;
    protected $mValue;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        return [
            $this->name => [
                Type::EFIELD => $this->mName
            ]
        ];
    }
}
