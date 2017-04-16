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
            $this->constants = self::search($this->from, $this->prefix);
        }

        if ($flagged) {
            return array_filter($this->getConstants(), function ($flag)  { return $this->has($flag); });
        }

        return $this->constants;
    }

    static public function search($from = null, $prefix = '')
    {
        $constants = class_exists($from)
            ? (new \ReflectionClass($from))->getConstants()
            : get_defined_constants()
        ;

        if ('' !== $prefix) {
            foreach ($constants as $constant => $value) {
                if (0 !== strpos($constant, $prefix)) {
                    unset($constants[$constant]);
                }
            }
        }

        return $constants;
    }
}
