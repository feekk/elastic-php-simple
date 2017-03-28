<?php
namespace ElasticPhpSimple\Base;

use ElasticPhpSimple\Type;

class RangeAggs extends Aggs{

    public function __construct($field ,$as = null ,$flag = true){
        $this->field = $field;
        $this->as = is_null($as) ? $field :$as;
        $this->type = Type::ERANGE;
        $this->_params($flag);
    }

    //
    public function setRanges($ranges)
    {
        $ranges = $this->transRanges($ranges);
        if (empty($ranges)) {
            return $this;
        }
        $this->_setAttr(Type::ERANGES, $ranges);
        return $this;
    }

    //
    protected function transRanges($ranges)
    {
        if (is_array($ranges)) {
            $validRanges = array();
            foreach ($ranges as $key=>$value) {
                if (isset($value['<'])) {
                    $tmp['to'] = $value['<'];
                } elseif (isset($value['<='])) {
                    $tmp['to'] = $value['<='];
                } elseif (isset($value['to'])) {
                    $tmp['to'] = $value['to'];
                }

                if (isset($value['>'])) {
                    $tmp['from'] = $value['>'];
                } elseif (isset($value['>='])) {
                    $tmp['from'] = $value['>='];
                } elseif (isset($value['from'])) {
                    $tmp['from'] = $value['from'];
                }

                if (is_null($value)) {
                    $tmp['from'] = null;
                    $tmp['to'] = null;
                }

                if (!isset($tmp)) { continue; }
                $validRanges[] = $tmp;
                unset($tmp);
            }
        } elseif (is_null($ranges)) {
            $validRanges = array(
                array(
                    "from" => null,
                    "to" => null
                )
            );
        } else {
            return null;
        }

        return $validRanges;
    }

}
