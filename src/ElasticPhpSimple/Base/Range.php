<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;
use ElasticPhpSimple\ElasticPhpSimpleException;

class Range{
    protected $name = Type::ERANGE;
    protected $from=null;
    protected $to=null;
    protected $lower = null;
    protected $upper = null;
    protected $field;

    public function query($field ,$value){
        $this->field = $field; 
        if(!empty($value) && is_array($value)){
            foreach($value as $k =>$v){
                $this->parse($k ,$v);
            }
        }
        return $this->_build();
    }
    protected function _build(){
        $build = [
            $this->name => [
                $this->field =>[
                ]
            ]
        ]; 
        if(!is_null($this->lower)){
            $build[$this->name][$this->field][$this->lower] = $this->from;
        }
        if(!is_null($this->upper)){
            $build[$this->name][$this->field][$this->upper] = $this->to;
        }
        return $build;
    }
    protected function parse($key ,$value){
        switch($key){
        case '>':
            $this->lower = "gt";
            $this->from = $value;
            break;
        case '>=':
            $this->lower = "gte";
            $this->from = $value;
            break;
        case '<':
            $this->upper = "lt";
            $this->to = $value;
            break;
        case '<=':
            $this->upper = "lte";
            $this->to = $value;
            break;
        default :
            throw new ElasticPhpSimpleException("unknow symbol in range parse");
        }

    }
}
