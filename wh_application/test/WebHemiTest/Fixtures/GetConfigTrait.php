<?php

namespace WebHemiTest\Fixtures;

use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Glob;
use ArrayObject;

trait GetConfigTrait
{
    /**
     * 
     *
     * @return ArrayObject
     */
    protected function getConfig()
    {
        $config = [];

        // Load configuration from autoload path
        foreach (Glob::glob(__DIR__ . '/../../../config/autoload/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE) as $file) {
            $config = ArrayUtils::merge($config, include $file);
        }

        return new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
    }
}
