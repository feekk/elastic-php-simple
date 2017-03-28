<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class MaxAggs extends Aggs{
    
    public function __construct($field ,$as = null ,$flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::EMAX;
        $this->_params($flag);     
    }



}
