<?php

namespace junkbot\core;

/*
 * Simplistic memoization implementation.
 * Uses timestamps to keep values valid for some time.
 */
trait Cacheable {

    private $cache;

    // Time to keep cache. Must be set in classes that use this.
    private $keepTime;

    /*
     * Check if function result is already in cache, if not - call function
     */
    protected function callCached($function, $args=null) {
        /*
         * Get key for called function
         * Key is a md5 hash from function name + args
         * If args is an array first glue it into one string
         */
        $key = is_array($args) ? md5($function[1] . implode($args)) : md5($function[1]);
        // Check if there is already a valid cached value
        if ($this->isValid($key))
        {
            return $this->getCachedValue($key);
        }
        // Otherwise call the function
        else
        {
            // Execute function by name with passed args, checking if args is an array or single value
            $result = is_array($args) ? call_user_func_array($function, $args) : call_user_func($function, $args);
            // Store received result in cache for future calls
            $this->setCachedValue($key, $result);
            return $result;
        }
    }

    /*
     * Check if there is a valid record in cache
     */
    private function isValid($key) {
        // Check if key exists in cache, if it exists check if saved timestamp is less than 60 minutes old
        return isset($this->cache[$key]) && (time() - $this->cache[$key][1]) < $this->keepTime;
    }

    /*
     * Get value from cache
     */
    private function getCachedValue($key) {
        return $this->cache[$key][0];
    }

    /*
     * Write value to cache
     */
    private function setCachedValue($key, $value) {
        // Store value and current timestamp in cache
        $this->cache[$key] = [$value, time()];
    }
}
