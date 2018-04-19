# elastic-php-simple

**elastic-php-simple** is a simple elasticsearch dsl builder for php.

# descrpition

There are two different libraries：Buider and Model。

**Builder** is original 

**Model** is package from **Builder** than can be extended by other schema model

# Usage

`**Builder**`
    
    $builder = new Builder();
    $a = $builder->match('a', 3);
    $b = $builder->match('b', 5);
    #and conditions
    $builder->_and($a, $b);
    $builder->orderBy('c', 'asc');
    $builder->offset(0, 10);
    $dsl = $builder->build();
    
`**Model**`

    use ElasticPhpSimple\ResultParse;
    
    class EsModel extends Model{
        protected $id;
        public $buckets;
        public $aggs;
        public $result;

        public function __construct(){
            parent::__construct();
        }
                
        public function getBuckets(){
            if(is_array($this->_builder->buckets) && count($this->_builder->buckets)>0){
                foreach($this->_builder->buckets as $k=>$v){
                    $this->buckets[] = $v->field;
                }   
            }   
        } 
        
        public function getMertics(){
            if(is_array($this->_builder->mertics) && count($this->_builder->mertics)>0){
                foreach($this->_builder->mertics as $k=>$v){
                    $this->aggs[] = $v->as;
                }   
            }   
        }
        
        protected function getParams(){
            $this->getBuckets();
            $this->getMertics();
        }   

        public function getList($orderName = '', $orderType = ''){
            $this->getParams();
            $dsl = $this->dsl();
            $this->result = Handle::getInstance()->search($dsl);
            $parse = new ResultParse($this);
            return $parse->getList($orderName, $orderType);
        }   

        public function getAggs(){
            $this->getParams();
            $dsl = $this->dsl();
            $this->result = Handle::getInstance()->search($dsl);
            $parse = new ResultParse($this);
            return $parse->getAggs();
        }   

        public function find(){
            $dsl = $this->dsl(); 
            $ret = Handle::getInstance()->search($dsl);
            return $ret;
        }   

        public function findById($id=null){
            if(is_null($id)){
                $id = $this->id;
            }
            $this->match('Id',$id);
            return $this->find();
        }

        public function Id(){
            return $this->id;
        }
    }
    
    class UserModel extends EsModel{ 
        public function _Fileds(){
            return [
                'Id'=>'',
                'Name'=>'',
                'Age'=>'',
            ];
        }
    }
    
    $user = new UserModel();
    
    $user->match('Id', 3);
    $user->match('Name', 'feek')->withInner('Id', 'or');
    $user->match('Age', 18)->withOuter('Id', 'and'); //like sql: (Id = 3 or Name='feek') and Age=18
    $list = $user->getList();
    


#API

##Builder

###conditions

**`match`**

    /**
     * equal condition
     * return array condition dsl
     */
    function match(string $name, string $value)
    
**`notMatch`**

    /**
     * not equal condition
     * return array condition dsl
     */
    function notMatch(string $name, string $value)
    
**`in`**

    /**
     * in condition
     * return array condition dsl
     */
    function in(int $name, array $value)
    
**`notIn`**

    /**
     * not in condition
     * return array condition dsl
     */
    function notIn(int $name, array $value)

**`range`** 

    /**
     * range condition
     * exsample: $value = array(">=" => 3, "<"=> 8)  like this: $name >= 3 and $name < 8
     * return array condition dsl
     */
    function range(string $name, array $value)

**`like`**  

    /**
     * like condition
     * return array condition dsl
     */
    function like(string $name, string $value)
    
**`notLike`**   

    /**
     * not like condition
     * return array condition dsl
     */
    function notLike(string $name, string $value)
    
###Builder condition builder

`this condition builder package condtions into dsl query body`
    
    $a = $builder->match('a', 2);
    $b = $builder->match('b', 3);
    $builder->_and($a,$b); //this func will builder $a and $b conditon with and
    
    result:
    { 
        "query": {
        "bool": {
          "must": {
            "bool": {
              "must": [
                {
                  "match": {
                    "a": {
                      "query": 2,
                      "type": "phrase"
                    }
                  }
                },
                {
                  "match": {
                    "b": {
                      "query": 3,
                      "type": "phrase"
                    }
                  }
                }
              ]
            }
          }
        }
      },
      "from": 0,
      "size": 10
    }

**`_or`**

    /**
     * gather condition with or
     * return array (queryBody)
     */
    function _or(args...) //allow mutil params

**`_and`**

    /**
     * gather condition with and
     * return array (queryBody)
     */
    function _and(args...) //allow mutil params
    
    
###aggs buckets

**`grouBy`**
    
    /**
     * aggs buckets by field
     * return ElasticPhpSimple\Base\TermsAggs
     */
    function grouBy($field, $as = null, $flag=true) //grouBy('Name', 'otherName')
    
**`dateHistogram`**

    /**
     * aggs buckets  by field
     * return ElasticPhpSimple\Base\DateHistogramAggs
     */
    function dateHistogram($field, $as = null, $flag=true)
    
**`histogram`**

    /**
     * aggs buckets  by field
     * return ElasticPhpSimple\Base\HistogramAggs
     */
    function histogram($field, $as = null, $flag=true)
    
###aggs mertics
    
**`min`**

    /**
     * aggs mertics by field
     * return ElasticPhpSimple\Base\MinAggs
     */
    function min($field, $as = null, $flag=true)
    
**`max`**

    /**
     * aggs by field
     * return ElasticPhpSimple\Base\MaxAggs
     */
    function max($field, $as = null, $flag=true)
    
**`sum`**

    /**
     * aggs by field
     * return ElasticPhpSimple\Base\SumAggs
     */
    function sum($field, $as = null, $flag=true)
    
**`avg`**

    /**
     * aggs by field
     * return ElasticPhpSimple\Base\AvgAggs
     */
    function avg($field, $as = null, $flag=true)    
**`count`**

    /**
     * aggs by field
     * return ElasticPhpSimple\Base\AvgAggs
     */
    function count($field, $as = null, $flag=true)

**`cardinalCount`**

    /**
     * distinct count aggs by field
     * return ElasticPhpSimple\Base\CardinalAggs
     */
    function cardinalCount($field, $as = null, $flag=true)
    

###sort

**`orderBy`**
    
    /**
     * order query body result list
     * return array (queryBody)
     */
    function orderBy($orderBy, $order) //orderBy('Name', 'asc')

**`grouByOrder`**
    
    /**
     * order aggs body result list
     * return array (queryBody)
     */
    function grouByOrder($orderBy, $order) //grouByOrder('Name', 'asc')
    
###other

**`build`**
    
    /**
     * return dls
     * return array 
     */
    function build()


##Model

Basically the **model** function is the same as the **Builder** except **Builder** function `_or` and `_and` replace by `withInner`/`withOuter `.

function `withInner/withOuter` is stick condtion's function, like (xxx and/or xxx) in sql

exsample:
    
    #where ((a=3 and c=5) or m=5) and (d=6 or h=8)
    
    $model->match('a', 3); //create first conditon, then we got and a variable
     
    //withInner means condtion will be stick in same level. 
    //it means we create other conditon and push it into a variable with and, 
    //then a and c in same level. like (a=3 and c=5)
    $model->match('c', 5)->withInner('a', 'and');
    
    //withInner means condtion will be stick in highter level.
    //a is and array with a and c in same level above
    //this action means push a into m with same level
    $model->match('m', 5)->withOuter('a', 'or');// we push m condition with same level with a
    
    $model->match('d', 6)->withOuter('a', 'and');
    
    $model->match('h', 8)-> withInner('d', 'or');
    
code:

$model->match('a', 3);

    $a=array(
        'field'=>'a',
        'value'=>3,
        'type'=>'match'
    )
    
$model->match('c', 5)->withInner('a', 'and');

    **//$a change!!!!**
    $a=array(       
        '_and'=>array(
            array(
                'field'=>'a',
                'value'=>3,
                'type'=>'match'
            ),
            $c
        )
    )
    $c = array(
        'field'=>'c',
        'value'=>5,
        'type'=>'match'
    )
    
$model->match('m', 5)->withOuter('a', 'or');

    **//$a change once again!!!!**
    $a=array(       
        '_or'=>array(
            array(
                '_and'=>array(
                    array(
                        'field'=>'a',
                        'value'=>3,
                        'type'=>'match'
                    ),
                    $c
                )
            ),
            $m
        )
    )
    $c = array(
        'field'=>'c',
        'value'=>5,
        'type'=>'match'
    )
    $m = array(
        'field'=>'m',
        'value'=>5,
        'type'=>'match'
    )
    
$model->match('d', 6)->withOuter('a', 'and');

    **//$a change once more!!!!**
    $a=array(       
        '_and'=>array(
            array(
                '_or'=>array(
                    array(
                        '_and'=>array(
                            array(
                                'field'=>'a',
                                'value'=>3,
                                'type'=>'match'
                            ),
                            $c
                        )
                    ),
                    $m
                )
            ),
            $b
        )
    )
        
    $c = array(
        'field'=>'c',
        'value'=>5,
        'type'=>'match'
    )
    $m = array(
        'field'=>'m',
        'value'=>5,
        'type'=>'match'
    )
    $d = array(
        'field'=>'d',
        'value'=>6,
        'type'=>'match'
    )
    
$model->match('h', 8)-> withInner('d', 'or');

    $a=array(
        '_and'=>array(
            array(
                '_or'=>array(
                    array(
                        '_and'=>array(
                            array(
                                'field'=>'a',
                                'value'=>3,
                                'type'=>'match'
                            ),
                            $c
                        )
                    ),
                    $m
                )
            ),
            $b
        )
    )
        
    $c = array(
        'field'=>'c',
        'value'=>5,
        'type'=>'match'
    )
    $m = array(
        'field'=>'m',
        'value'=>5,
        'type'=>'match'
    )
    **//d change because withInner d**
    $d = array( 
        '_or'=>array(
            array(
                'field'=>'d',
                'value'=>6,
                'type'=>'match'
            ),
            $h
        )
    )   
    $h = array(
        'field'=>'h',
        'value'=>8,
        'type'=>'match'
    )
    
    
    


    
