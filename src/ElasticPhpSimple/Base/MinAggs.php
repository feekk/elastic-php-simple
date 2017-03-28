<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class MinAggs extends Aggs{
    
    public function __construct($field ,$as = null ,$flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::EMIN;
        $this->_params($flag);     
    }



}
