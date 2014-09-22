<?php
namespace HairConnect\Transformers;

/**
 * Abstract Class BarbersTransformer
 * @package HairConnect\Transformers
 */
abstract class Transformers{
    
    /**
     * Transformss a collection of data into json
     * @param array $items
     */
    public function transformCollection(array $items){
        return array_map([$this, 'transform'], $items);
    }

    /**
     * Transformss a set of data into json
     * @param object $items
     * @return array
     */
    public abstract function transform($items);
}