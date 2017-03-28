<?php
namespace ElasticPhpSimple\Base; 

use ElasticPhpSimple\Type;

class TermsAggs extends Aggs{

    public function __construct($field , $as = null, $flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::ETERMS;
        $this->_params($flag); 
    }

    /**
     * include
     * include value if term by array field
     */
    public function in($val){
        $this->_setAttr(Type::EINCLUDE ,$val);
        return $this;
    }
    /**
     * exclude
     * exclude value if term by array field
     */
    public function notIn($val){
        $this->_setAttr(Type::EEXCLUDE ,$val);
        return $this;
    }
    /**
     * size
     * set size in terms
     */
    public function size($size){
        $this->_setAttr(Type::ESIZE ,$size);
        return $this;
    }
    
}

