<?php
namespace ElasticPhpSimple;

use ElasticPhpSimple\Base;

class ResultParse{

    protected $_resultList = [];
    protected $_buckets  = [];
    protected $_took = '';
    protected $_timed_out = '';
    protected $_shards = '';
    protected $_total = '';
    protected $_hits = '';
    protected $_hitsList = [];
    protected $_aggregations = [];
    protected $_aggsList = [];
    protected $sortKey = '';
    protected $sortType = '';
    protected $_aggKeys = [];
    protected $_modelFileds = [];
    static $i = 0;

    public function __construct($builder) {
        $this->_resultList = empty($builder->result) ? [] : $builder->result;
        $this->_buckets = empty($builder->buckets) ? [] : $builder->buckets;
        $this->_aggs = empty($builder->aggs) ? [] : $builder->aggs;
        $this->_modelFileds = method_exists($builder, '_Fileds') ? $builder->_Fileds() : [];
        //$this->_buckets = $buckets;
        $this->_resultParse();
    }

    public function _resultParse() {
        foreach($this->_resultList as $key => $value) {
            $name = '_'.$key;
            $this->$name = $value;
        }
        return $this;
    }

    public function _hitsParse() {
        $temp = [];
        if(!empty($this->_hits)){
             $temp = $this->_hits['hits'];
        }
        if(!empty($temp)){
            foreach($temp as $v){
                $this->_hitsList[] = $v['_source'];
            }
        }
        return $this;
    }

    public function _aggsParse() {
        foreach($this->_aggregations as $n => $v) {
            $this->_aggsList[] = [$n=>$v['value']];
        }
        return $this;
    }

    public function _termsParse() {
        $r = [];
        $this->_recurseParse($this->_aggregations, $this->_buckets[self::$i], $r);
        $this->_chunkData($r);
        return $this;
    }

    protected function _recurseParse($data, $key, &$res) {
        if(isset($data[$key]['buckets'])){
            $nextKey = isset($this->_buckets[++self::$i]) ? $this->_buckets[self::$i] : '';
            foreach($data[$key]['buckets'] as $val){
                $res[] = isset($val['key_as_string']) ? [$key=>$val['key_as_string']] : [$key=>$val['key']];
                $this->_recurseParse($val, $nextKey, $res);
                if($nextKey == '' && !empty($this->_aggs)){
                    foreach($this->_aggs as $aggsName){
                        $res[] = [$aggsName => $val[$aggsName]['value']];
                    }
                }
            }
        }
        $nowK = array_keys($this->_buckets, $key);
        self::$i = array_pop($nowK);
    }

    protected function _chunkData($data){
        $i = 0;
        $tempList = array_merge($this->_buckets, $this->_aggs);
        $r = [];
        while(!empty($data)){
            foreach($tempList as $key){
                $kv = array_shift($data);
                if(isset($kv[$key]) && !is_null($kv[$key])){
                    $r[$i][$key] = $kv[$key];
                }else{
                    $r[$i][$key] = $r[$i-1][$key];
                    array_unshift($data, $kv);
                }
            }
            $i++;
        }
        $this->_aggsList = $r;
    }

    public function getList($orderName = '', $orderType = '') {
        try {
            if (!empty($this->_aggregations)) {
                if (!empty($this->_buckets)) {
                    //$this->_termsParse();
                    $this->recurAggs($this->_aggregations, $this->_aggKeys);
                    //填数
                    if ($orderName != '' && $orderType != '') {
                        $this->resultSort($orderName, $orderType);
                    }
                    return $this->_aggsList;
                } else {
                    return $this->_aggsParse()->_aggsList;
                }
            } else {
                $list = $this->_setDefList();
                return ['total' => $this->_hits['total'], 'list' => $list];
            }
        } catch (\Exception $e){
            \Log::warning("GetList|GetListException", ['aggs'=>$this->_aggregations, 'key'=>$this->_aggKeys]);
            throw $e;
        }
    }

    protected function _setDefList(){
        $list = $this->_hitsParse()->_hitsList;
        if(!empty($this->_modelFileds)){
            foreach($list as &$data){
                foreach($this->_modelFileds as $dataCol=>$dataDef){
                    if(!isset($data[$dataCol])){
                        $data[$dataCol] = $dataDef;
                    }
                }
            }
        }
        return $list;
    }

    public function getAggs() {
        $aggsRes = $this->getList();
        $aggsRes = array_pop($aggsRes);
        list($k, $v) = each($aggsRes);
        return $v;
    }

    protected function _sortFunc($a, $b){
        if(isset($a[$this->sortKey]) && isset($b[$this->sortKey])){
            if($this->sortKey == 'Date'){
                $aNum = strtotime($a['Date']);
                $bNum = strtotime($b['Date']);
            }else{
                $aNum = $a[$this->sortKey];
                $bNum = $b[$this->sortKey];
            }
            if($aNum == $bNum)
                return 0;
            if($this->sortType == 'desc'){
                return $aNum > $bNum ? -1 : 1;
            }else{
                return $aNum > $bNum ? 1 : -1;
            }
        }
    }

    public function resultSort($name, $sortType){
        $this->sortKey = $name;
        $this->sortType = $sortType;
        uasort($this->_aggsList, [$this, '_sortFunc']);
        return $this->_aggsList;
    }

    protected function recurAggs($da ,$ky){
	    $b = array_merge($this->_buckets, $this->_aggs);
	    foreach($da as $_k => $_r){
		    if(in_array($_k,$b) && isset($da[$_k]['buckets'])){
			    $ky['pre'] = $_k;
			    $this->recurBuckets($da[$_k]['buckets'], $ky);
		    }
	    }
    }

    protected function recurBuckets($buckets, $prekeys){
	    $b = array_merge($this->_buckets, $this->_aggs);
	    $f=true;
	    foreach($buckets as $row){
		    $tmp = $prekeys;
		    if(isset($row['key_as_string'])){
			    $tmp[$tmp['pre']] = $row['key_as_string'];
		    }else{
			    $tmp[$tmp['pre']] = $row['key'];
		    }
		    foreach($row as $key=>$value){
			    if(in_array($key,$b) ){
				    if(!isset($value['buckets'])){
					    $tmp[$key] = $value["value"];
				    }else{
					    $f=false;
					    $this->recurAggs([$key=>$value],$tmp);
				    }
			    }
		    }
		    if($f){
			    $this->_aggsList[] = $tmp;
		    }
	    }
    }
}
