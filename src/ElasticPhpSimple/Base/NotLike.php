<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class NotLike{
    protected $mName ;
    protected $mValue;

    public function query($name ,$value){
        $this->mName = $name;
        $this->mValue = $value;
        return $this->_build();
    }

    protected function _build(){
        $notlike = new NotLike();
        return [
            Type::EBOOL => [
                Type::EMUSTNOT => $notlike->query($this->mName ,$this->mValue)
            ] 
        ]; 
    }
}
