<?php
namespace ElasticPhpSimple\Base; 

use ElasticPhpSimple\Type;

class Aggs{
    protected $field;
    protected $type; 
    protected $as;
    protected $params=[];
    
    /**
     * init aggs struct by agg type
     */
    protected function _params($flag){
        isset($this->params[$this->as]) || ($this->params[$this->as] = []);
        isset($this->params[$this->as][$this->type]) || ($this->params[$this->as][$this->type] = []);
        $flag && $this->_setAttr(Type::EFIELD ,$this->field);
    }
    /**
     * set field to type array
     */
    protected function _setAttr($name ,$value){
        $this->params[$this->as][$this->type][$name] = $value; 
    }
    /**
     * get field from type array 
     */
    protected function _getAttr($name){
        return isset($this->params[$this->as][$this->type][$name])?$this->params[$this->as][$this->type][$name] : null;
    }
    /**
     * set filed to type pulic
     */
    public function setAttr($name ,$value){
        $this->_setAttr($name ,$value);
        return $this;
    }
    /**
     * set script to type public
     */
    public function setScript($script){
        $this->_setAttr(Type::ESCRIPT ,$script);
        return $this;
    }
    /**
     * mutil aggs append behide other aggs
     */
    public function append($aggs){
        isset($this->params[$this->as][Type::EAGGS]) || ($this->params[$this->as][Type::EAGGS]=$aggs);
        return $this;
    } 
    /**
     * return aggs result
     */
    public function build($type=Type::EBUCKETS){
        if($type == Type::EBUCKETS){
            $ret = $this->params;
        }elseif($type == Type::EMERTICS){
            $_d = each($this->params); 
            $ret[] = $_d['key'];
            $ret[] = $_d['value'];
        }
        return $ret;
    }
    /**
     * order
     * order values after aggs action
     */
    public function order($orderBy ,$order){
        $data = $this->_getAttr(Type::EORDER);
        if(is_null($data)){
            $data = [];
        }
        array_push($data ,[$orderBy=>$order]);
        $this->_setAttr(Type::EORDER ,$data) ;
        return $this;
    }
    public function orderTerm($order){
        return $this->order("_term",$order);
    }
    public function orderCount($order){
        return $this->order("_count",$order);
    }
    public function orderKey($order){
        $this->_setAttr(Type::EORDER, ["_key"=>$order]) ;
        return $this;
    }
    /**
     * format from php format to elasticsearch aggs format
     */
    public function dateFormat($format){
        $this->_setAttr(Type::EFORMAT ,$this->transforDate($format));
        return $this;
    }
    /**
     * input Y-m-d H:i:s
     */ 
    protected function transforDate($format){
        $format = str_replace('Y','yyyy',$format);
        $format = str_replace('m','MM',$format);
        $format = str_replace('d','dd',$format);
        $format = str_replace('H','HH',$format);
        $format = str_replace('i','mm',$format);
        $format = str_replace('s','ss',$format);
        $format = str_replace('W','ww',$format);
        return $format;
    }

    public function __get($name){
        return isset($this->$name)?$this->$name:null;
    }
}

