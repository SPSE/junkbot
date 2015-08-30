<?php

namespace junkbot\core;

trait Cacheable {

    private $cache;

    protected function callCached($function, $args=null) {
        $key = is_array($args) ? md5($function[1] . implode($args)) : md5($function[1]);
        if ($this->isValid($key))
        {
            return $this->getCachedValue($key);
        }
        else
        {
            $result = is_array($args) ? call_user_func_array($function, $args) : call_user_func($function, $args);
            $this->setCachedValue($key, $result);
            return $result;
        }
    }

    private function isValid($key) {
        return isset($this->cache[$key]) && (time() - $this->cache[$key][1]) < 3600;
    }

    private function getCachedValue($key) {
        return $this->cache[$key][0];
    }

    private function setCachedValue($key, $result) {
        $this->cache[$key] = [$result, time()];
    }
}