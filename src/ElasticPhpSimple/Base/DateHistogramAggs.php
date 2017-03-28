<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class DateHistogramAggs extends HistogramAggs{
    
    public function __construct($field, $as = null, $flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::EDATEHISTOGRAM;
        $this->_params($flag);
    }
    

}
