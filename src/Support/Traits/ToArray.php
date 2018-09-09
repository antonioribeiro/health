<?php

namespace PragmaRX\Health\Support\Traits;

use Illuminate\Contracts\Support\Arrayable;

trait ToArray
{
    private static $__depth;

    private static $__maxDepth;

    /**
     * Reset depth and convert object to array
     *
     * @param $subject
     * @param int $maxDepth
     * @return array|null
     */
    public function __toArray($subject, $maxDepth = 3)
    {
        static::$__depth = 0;

        static::$__maxDepth = $maxDepth;

        return $this->___toArray($subject, $maxDepth);
    }

    /**
     * Convert object to array
     *
     * @param $subject
     * @return array|null
     */
    public function ___toArray($subject)
    {
        $callback = $this->__getToArrayCallBack();

        return $callback($subject);
    }

    /**
     * Generate a callback to transform object to array.
     *
     * @return \Closure
     */
    public function __getToArrayCallBack()
    {
        return function ($subject) {
            static::$__depth++;

            if ($subject instanceof Arrayable) {
                $subject = $subject->toArray();
            }

            if (is_object($subject)) {
                $subject = get_object_vars($subject);
            }

            if (is_array($subject)) {
                if (static::$__depth <= static::$__maxDepth) {
                    $subject = array_map(
                        $this->__getToArrayCallBack(),
                        $subject
                    );
                } else {
                    $subject = null;
                }
            }

            static::$__depth--;

            return $subject;
        };
    }
}
