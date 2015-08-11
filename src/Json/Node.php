<?php

namespace Commercetools\Commons\Json;

class Node implements \JsonSerializable
{
    protected $root;
    protected $parent;
    protected $data;
    protected $initialized = [];
    protected $context;

    final private function __construct($data = [], $context, Node $root = null, Node $parent = null)
    {
        $this->root = is_null($root) ? $this : $root;
        $this->parent = $parent;
        $this->data = new \ArrayIterator($data);
        $this->context = $context;
    }

    public function __call($method, $arguments)
    {
        $action = substr($method, 0, 3);
        $field = lcfirst(substr($method, 3));

        switch ($action) {
            case 'get':
                return $this->get($field);
            case 'set':
                return $this->set($field, isset($arguments[0]) ? $arguments[0] : null);
        }
        throw new \BadMethodCallException();
    }

    public function set($field, $value)
    {
        $this->data->offsetSet($field, $value);
    }

    public function get($field)
    {
        if (isset($this->initialized[$field])) {
            return $this->data[$field];
        }

        $children = null;
        if (isset($this->data[$field])) {
            $children = $this->data[$field];
        }
        $this->initialized[$field] = true;
        $this->data[$field] = static::createNodeObject($children, $this->context, $this->root, $this);

        return $this->data[$field];
    }

    /**
     * @param $node
     * @param $context
     * @param Node $root
     * @param Node $parent
     * @return mixed
     */
    protected static function createNodeObject($node, $context = null, Node $root = null, Node $parent = null)
    {
        if (is_object($node)) {
            return new Node($node, $context, $root, $parent);
        }
        if (is_array($node)) {
            return new NodeCollection($node, $context, $root, $parent);
        }
        return $node;
    }

    /**
     * @param array $data
     * @param null $context
     * @return mixed
     */
    final public static function of($context = null)
    {
        return static::createNodeObject(new \stdClass(), $context);
    }

    final public static function ofData($data, $context = null)
    {
        return static::createNodeObject($data, $context);
    }

    public function toArray()
    {
        return $this->data->getArrayCopy();
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}