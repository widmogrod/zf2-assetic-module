<?php
namespace Assetic;

class Configuration
{
    protected $data;

    public function __construct(array $data)
    {
        if ($data instanceof Traversable) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = iterator_to_array($data, true);
            }
        } elseif (!is_array($data)) {
            throw new Exception\InvalidArgumentException(
                'Configuration data must be of type Zend\Config\Config or an array'
            );
        }

        $this->data = $data;
    }
}
