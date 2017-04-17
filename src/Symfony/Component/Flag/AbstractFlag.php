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
    protected $bitfield;
    protected $flags = null;

    const FLAG_MAX_VALUE = 2147483647; // 2^31−1.

    public function __construct($from = false, $prefix = '', $bitfield = 0)
    {
        $this->from = $from;
        $this->prefix = $prefix;
        $this->set($bitfield);
    }

    function __toString()
    {
        return sprintf(
            '[dec: %s] [bin: %b] [flags: %s]',
            $this->bitfield,
            $this->bitfield,
            implode(' | ', array_keys($this->getFlags(true)))
        );
    }

    public function get()
    {
        return $this->bitfield;
    }

    public function set($bitfield)
    {
        $this->bitfield = $bitfield;

        return $this;
    }

    public function getFlags($flagged = false)
    {
        if (null === $this->flags) {
            $this->flags = array();
        }

        if (false !== $this->from) {
            $this->flags = self::search($this->from, $this->prefix);
        }

        if ($flagged) {
            return array_filter($this->getFlags(), function ($flag)  { return $this->has($flag); });
        }

        return $this->flags;
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
