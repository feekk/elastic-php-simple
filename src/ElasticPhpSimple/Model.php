<?php
namespace ElasticPhpSimple;

class Model{
    CONST _AND = "_and";
    CONST _OR = "_or";
    protected $_doc;
    protected $_prev_index;
    protected $_conditions=[];
    protected $_remove_keys=[];
    protected $_builder;
    protected $_order=[];

    public function __construct(){
        $this->_builder = new Builder();
    }
    public function getBuilder(){
        return $this->_builder;
    }
    public function esIndex(){
        return "index";
    }   
    public function esType(){
        return "type";
    }

    protected function _filedIndex($field){
        return '_index_'.$field;
    } 

    public function select($field){
        return $this->_builder->select($field);
    }
    
    public function term($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function notTerm($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function match($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function notMatch($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function in($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function notIn($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function like($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function notLike($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function range($field ,$value, $as=null){
        return $this->condition($field ,$value ,__FUNCTION__, $as);
    }
    public function missing($field,$as=null){
        return $this->condition($field ,"" ,__FUNCTION__, $as);
    }
    public function notMissing($field, $as=null){
        return $this->condition($field ,"" ,__FUNCTION__, $as);
    }
    public function condition($field ,$value ,$func, $fieldAs=null){
        $_field = $field;
        if(!is_null($fieldAs)){
           $_field = $fieldAs;  
        }

        $this->$_field = [[$field ,$value ,$func]];
        $index = $this->_filedIndex($_field);
        //push
        $this->$index = array_push($this->_conditions ,'$'.$_field);
        //index
        $this->_prev_index = --$this->$index;
        return $this; 
    }
    public function withInner($field ,$type="and"){
        return $this->with($field ,$type ,'in');
    }
    public function withOuter($field ,$type='and'){
        return $this->with($field ,$type ,'out');
    } 
    protected function with($field ,$type='and' ,$loc='in'){
        $type = '_'.strtolower($type);
        if(in_array($type , [self::_AND ,self::_OR])){
            $prevField  = $this->_conditions[$this->_prev_index];
            $prevIndex = $this->_filedIndex($prevField);
            $withIndex = $this->_filedIndex($field);
            if(!isset($this->$field)){
                //TODO
               return $this; 
            }
            $playload = $this->$field;
            if($loc == 'in'){
                if(!isset($playload[$type])){
                    $playload = [$type=>$playload];
                } 
                array_push($playload[$type] ,$prevField);
            }elseif($loc == 'out'){
                if(isset($playload[self::_AND]) || isset($playload[self::_OR])) {
                    $playload = [$playload];
                }
                $playload = [$type=>$playload];
                array_push($playload[$type] ,$prevField);
            }
            $this->_remove_keys[] = $this->_prev_index;
            $this->$field = $playload;
        }
        return $this;
    }
    public function groupByOrder($field ,$order){
        return $this->_builder->groupByOrder($field ,$order);
    }
    public function groupBy($field ,$in=null ,$flag=true){
        return $this->_builder->groupBy($field ,$in ,$flag);
    }
    public function dateHistogram($field ,$in=null ,$flag=true){
        return $this->_builder->dateHistogram($field ,$in ,$flag);
    }
    public function histogram($field ,$in=null ,$flag=true){
        return $this->_builder->histogram($field ,$in ,$flag);
    }
    public function orderBy($orderBy ,$order){
        return $this->_builder->orderBy($orderBy ,$order) ;
    }
    public function offset($from=0,$size=10){
        return $this->_builder->offset($from ,$size) ;
    }
    public function min($field ,$as=null ,$flag=true){
        return $this->_builder->min($field ,$as ,$flag);
    }
    public function max($field ,$as=null ,$flag=true){
        return $this->_builder->max($field ,$as ,$flag);
    }
    public function sum($field ,$as=null ,$flag=true){
        return $this->_builder->sum($field ,$as ,$flag);
    }
    public function avg($field ,$as=null ,$flag=true){
        return $this->_builder->avg($field ,$as ,$flag);
    }
    public function count($field ,$as=null ,$flag=true){
        return $this->_builder->count($field ,$as ,$flag);
    }
    public function distinctCount($field ,$as=null ,$flag=true){
        return $this->_builder->cardinalCount($field ,$as ,$flag);
    }
    public function rangeAgg($field, $as=null, $flag=true){
        return $this->_builder->rangeAgg($field, $as, $flag);
    }
    public function dsl(){
        //process conditions
        if(!empty($this->_conditions)){
            //get root field    
            if(!empty($this->_remove_keys)){
                foreach($this->_remove_keys as $_index){
                    if(isset($this->_conditions[$_index])){
                        unset($this->_conditions[$_index]);
                    }
                }
            }
            $firstField = current($this->_conditions);
            $ret = $this->parse($firstField); 
            //last build 
            $field = str_replace('$','',$firstField);
            $data = $this->$field;
            if(!isset($data[self::_AND]) && !isset($data[self::_OR])){
                $this->_builder->_and($ret);
            }
        }
        $this->_doc = [
            Type::EINDEX => $this->esIndex(),
            Type::ETYPE => $this->esType(),
            Type::EBODY => $this->_builder->build()
        ]; 
        return $this->_doc;
    }
    
    protected function parse($data){
        $conditions =[] ;
        $_and_condition=[];
        $_or_condition=[];
        //1、变量处理获取的数据
        if(is_string($data) && 0 === strpos($data ,'$')){
            //获取变量数据  
            $data = str_replace('$','',$data);
            $data = $this->$data;
            if(!isset($data[self::_AND]) && !isset($data[self::_OR])){
                $data = current($data);
            }
        }
        //2、数组处理获取的数据      
        //只有and 或者or条件才会有多条
        if(isset($data[self::_AND])){
            foreach($data[self::_AND] as $row){
                $_and_condition[] = $this->parse($row);
            }
            $conditions = call_user_func_array(array($this->_builder ,self::_AND),$_and_condition);
        }elseif(isset($data[self::_OR])){
            foreach($data[self::_OR] as $row){
                $_or_condition[] = $this->parse($row);
            }
            $conditions = call_user_func_array(array($this->_builder ,self::_OR),$_or_condition);
        }else{
            //只有一条 直接处理 
            $conditions = $this->call($data);
        }
        return $conditions;
    }
    protected function call($data){
       return $this->_builder->$data[2]($data[0] ,$data[1]); 
    }
    public function __set($name ,$value){
        $this->$name = $value;
    }
    public function __get($name){
        return isset($this->$name)?$this->$name:null;
    }
}
