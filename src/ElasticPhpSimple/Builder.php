<?php

namespace ElasticPhpSimple;

use ElasticPhpSimple\Base;

class Builder{
    protected $query = [];
    protected $queryBody=[];
    protected $aggs = [];
    protected $sort = [];
    protected $from = 0;
    protected $size = 10;
    protected $buckets=[];
    protected $mertics=[];
    protected $order=[];
    protected $source=[];
    protected $includes=[];
    protected $excludes=[];
    const MaxLimit = 10000;

    public function __construct(){
        $this->body = [
            //Type::EQUERY=>&$this->query,
            Type::EQUERY=>&$this->queryBody,
            Type::ESORT=>&$this->sort,
            Type::EAGGS=>&$this->aggs,
            Type::EOFFSET=>&$this->from,
            Type::ESIZE=>&$this->size,
            Type::ESOURCE => &$this->source,
        ];
        $this->queryHead();
    }
    protected function queryHead($all=false){
        if(!$all){
            /*
            $this->query = [
                Type::EBOOL => [
                    Type::EMUST => &$this->queryBody
                ]
            ];
             */
            $this->query = &$this->queryBody;
        }else{
            //$this->query = [
            $this->queryBody = [
                Type::EMATCHALL => []
            ];
        }
    }
    public function offset($from=0,$size=10){
        $this->from = $from;
        $this->size = $size;
        if($this->from < 0 || $this->size < 0){
            throw new \Exceptions('offset from and size must be > 0');
        }
        if($this->from > self::MaxLimit){
            $this->from = self::MaxLimit; 
            $this->size = 0;
        }else if( ($this->from + $this->size) > self::MaxLimit){
            $this->size = self::MaxLimit - $this->from;
        }
        return $this;
    }
    public function select($select =null){
        if(!empty($select) && $select != "*"){
            $selectArr = explode(',', $select); 
            if(!empty($selectArr) && is_array($selectArr)){
                foreach($selectArr as $field){
                    if(!in_array(trim($field), $this->includes)) {
                        array_push($this->includes, trim($field));
                    }
                }
            }elseif(is_string($selectArr)){
                array_push($field, $this->includes);
            }
        } 
    }
    public function term($name, $value){
        $term = new Base\Term();
        return $term->query($name, $value);
    } 
    public function notTerm($name, $value){
        $notTerm = new Base\NotTerm();
        return $notTerm->query($name, $value);
    }
    public function match($name ,$value){
        $match = new Base\Match();
        return $match->query($name ,$value);
    }
    public function notMatch($name ,$value){
        $notMatch = new Base\NotTerm();
        return $notMatch->query($name ,$value);
    }
    public function in($name ,$value){
        $in = new Base\In();
        return $in->query($name ,$value);
    }
    public function notIn($name ,$value){
        $notin = new Base\NotIn();
        return $notin->query($name ,$value);
    }
    public function range($name ,$value){
        $range = new Base\Range();
        return $range->query($name ,$value);
    }
    public function like($name ,$value){
        $notin = new Base\Like();
        return $notin->query($name ,$value);
    }
    public function notLike($name ,$value){
        $notin = new Base\NotLike();
        return $notin->query($name ,$value);
    }
    public function groupByOrder($name ,$value){
        $this->order[$name] = [$name, $value];
        return $this;
    }
    public function missing($name, $value){
        $missing = new Base\Missing();
        return $missing->query($name, $value);
    }
    public function notMissing($name ,$value){
        $notin = new Base\NotMissing();
        return $notin->query($name ,$value);
    }
    public function _and(){
        $merge = [];
        if(!empty($this->queryBody)){
            //$merge[] = $this->queryBody;
        }
        $args = func_get_args();
        foreach($args as $struct){
            $merge[] = $struct; 
        }  
        if(count($merge) > 1){
            $merge = [
                Type::EBOOL => [
                    Type::EMUST=> $merge
                ]
            ];
        }elseif(count($merge)==1){
            $merge = current($merge);
        } 
        $this->queryBody = $merge;
        return $this->queryBody;
    }

    public function _or(){
        $merge = [];
        if(!empty($this->queryBody)){
            //$merge[] = $this->queryBody;
        }
        $args = func_get_args();
        foreach($args as $struct){
            $merge[] = $struct; 
        }  
        if(count($merge) > 1){
            $merge = [
                Type::EBOOL => [
                    Type::ESHOULD=> $merge
                ]
            ];
        }elseif(count($merge)==1){
            $merge = current($merge);
        }
        $this->queryBody = $merge;
        return $this->queryBody;
    }

    public function orderBy($orderBy ,$order){
        $sort = new Base\Sort(); 
        array_push($this->sort , $sort->order($orderBy ,$order));
        return $this;
    }

    public function groupBy($field ,$as = null ,$flag = true){
       $agg = new Base\TermsAggs($field, $as, $flag);  
       $a = &$agg;
       array_push($this->buckets ,$a);
       $a->size(500);
       return $a;      
    }
    public function dateHistogram($field ,$as=null ,$flag=true){
        $agg = new Base\DateHistogramAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->buckets ,$a);
        return $a;
    }
    public function histogram($field ,$as=null ,$flag=true){
        $agg = new Base\HistogramAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->buckets ,$a);
        return $a;
    }
    public function min($field ,$as=null ,$flag=true){
        $agg = new Base\MinAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->mertics ,$a);
        return $a;
    }
    public function max($field ,$as=null ,$flag=true){
        $agg = new Base\MaxAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->mertics ,$a);
        return $a;
    }
    public function sum($field ,$as=null ,$flag=true){
        $agg = new Base\SumAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->mertics ,$a);
        return $a;
    }
    public function avg($field ,$as=null ,$flag=true){
        $agg = new Base\AvgAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->mertics ,$a);
        return $a;
    }
    public function count($field ,$as=null ,$flag=true){
        $agg = new Base\CountAggs($field ,$as ,$flag);  
        $a = &$agg;
        array_push($this->mertics ,$a);
        return $a;
    }
    public function cardinalCount($field, $as = null, $flag = true){
        $agg  = new Base\CardinalAggs($field, $as, $flag);
        $a = &$agg;
        array_push($this->mertics, $a);
        return $a;
    }
    public function rangeAgg($field, $as=null, $flag=true){
        $agg = new Base\RangeAggs($field, $as, $flag);
        $a = &$agg;
        array_push($this->buckets, $a);
        return $a;
    }

    public function build(){
        //process mertics
        $mertics=[];
        if(!empty($this->mertics)){
            foreach($this->mertics as $mertic){
                list($field ,$row) = $mertic->build(Type::EMERTICS);
                $mertics[$field] = $row;
            } 
        }
        //process buckets
        if(!empty($this->buckets)){
            reset($this->buckets);
            if(!empty($this->order)){
                foreach($this->buckets as $key=>$bucket){
                    if(isset($this->order[$bucket->as])){
                        list($field ,$order) = $this->order[$bucket->as];
                        $this->buckets[$key]->order('_'.Type::ETERM ,$order);
                        unset($this->order[$bucket->as]);
                    }
                } 
            }
            $last = end($this->buckets);
            if(!empty($this->order)){
                foreach($this->order as $row){
                    list($field ,$order) = $row;
                    $last->order($field , $order);
                } 
            }
            if(!empty($mertics)){
                $last->append($mertics); 
            }
            while($prev = prev($this->buckets)){
                $last = $prev->append($last->build());
            }
            $this->aggs = $last->build(); 
        }else{
            $this->aggs = $mertics;
        }
        if(!empty($this->includes)){
            $this->source[Type::EINCLUDES] = $this->includes;
        }
        if(!empty($this->excludes)){
            $this->source[Type::EEXCLUDES] = $this->excludes;
        }
        foreach($this->body as $key =>$row){
            if($row == []){
                unset($this->body[$key]);
            } 
        }
        if(empty($this->queryBody)){
            $this->queryHead(true);
        } 
        
        return $this->body;
    }

    public function __get($name){
        return isset($this->$name)?$this->$name:null;
    }
}

