<?php
namespace HairConnect\Transformers;

abstract class Transformers{
    
    /**
     * Transforms a collection of data
     * @param array $items
     */
    public function transformCollection(array $items){
        return array_map([$this, 'transform'], $items);
    }

    /**
     * @param $items
     * @return mixed
     */
    public abstract function transform($items);
}