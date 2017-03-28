<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class NotMissing{
    protected $name = Type::EMISSING;
    protected $mName;
    protected $mValue;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        $m = new Missing();
        return [
            Type::EBOOL => [
                Type::EMUSTNOT => $m->query($this->mName ,$this->mValue)
            ]
        ];
    }
}
