<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Flag;

abstract class AbstractFlag implements FlagInterface
{
    protected $from;
    protected $prefix;
    protected $flags;
    protected $constants = null;

    public function __construct($from = false, $prefix = '')
    {
        $this->from = $from;
        $this->prefix = $prefix;
    }

    public function get()
    {
        return $this->flags;
    }

    public function set($flags)
    {
        $this->flags = $flags;

        return $this;
    }

    public function getConstants($flagged = false)
    {
        if (false === $this->from) {
            return array();
        }

        if (null === $this->constants) {
            $this->constants = class_exists($this->from)
                ? (new \ReflectionClass($this->from))->getConstants()
                : get_defined_constants()
            ;

            if ('' !== $this->prefix) {
                foreach ($this->constants as $constant => $value) {
                    if (0 !== strpos($constant, $this->prefix)) {
                        unset($this->constants[$constant]);
                    }
                }
            }
        }

        if ($flagged) {
            return array_filter($this->getConstants(), function ($flag)  { return $this->has($flag); });
        }

        return $this->constants;
    }
}
