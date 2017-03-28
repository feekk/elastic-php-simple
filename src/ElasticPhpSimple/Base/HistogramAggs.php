<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class HistogramAggs extends Aggs{
    
    public function __construct($field ,$as = null ,$flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::EHISTOGRAM;
        $this->_params($flag);
    }
    public function interval($value){
        $this->_setAttr(Type::EINTERVAL ,$value);
        return $this;
    }
    /**
     * default value for empty buckets
     * force return empty buckets
     */
    public function minDocCount($value){
        $this->_setAttr(Type::EMINDOCCOUNT , $value);
        return $this;
    }
    /**
     * force return range buckets range min and max
     */
    public function bounds($min,$max){
        $this->_setAttr(Type::EEXTENDEDBOUNDS ,[Type::EMIN=>$min ,Type::EMAX=>$max]);
        return $this;
    }
    public function format($format){
        return $this->dateFormat($format);
    }




}
