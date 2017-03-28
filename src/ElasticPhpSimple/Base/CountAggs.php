<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class CountAggs extends Aggs{
    
    public function __construct($field ,$as = null ,$flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::ECONT;
        $this->_params($flag);     
    }



}
