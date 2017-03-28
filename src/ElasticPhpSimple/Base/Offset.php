<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class Offset{
    protected $offset;
    protected $size;   
    
    public function set($size = 10 ,$offset = 0){
        $this->offset = $offset;
        $this->size = $size;
        return $this->_build();
    }
    protected function _build(){
        return [
            Type::EOFFSET => $this->offset,
            Type::ESIZE => $this->size
        ];
    }
}
