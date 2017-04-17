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

    /**
     * @return Flag
     */
    static public function create($from = false, $prefix = '', $hierarchical = false, $bitfield = 0)
    {
        // TODO throw InvalidArgumentException if $hierarchical && $forceToBinarize

        $onlyInt = true;
        $forceToBinarize = false;

        if (false === $from) {
            $forceToBinarize = true;
        } else {
            foreach (AbstractFlag::search($from, $prefix) as $value) {
                if (!is_int($value)) {
                    $onlyInt = false;
                    break;
                }
            }
        }

        switch (true) {
            case !$forceToBinarize && $onlyInt && !$hierarchical:
                $class = Flag::class;
                break;
            case !$forceToBinarize && $onlyInt && $hierarchical:
                $class = HierarchicalFlag::class;
                break;
            default:
                $class = BinarizedFlag::class;
        }

        return new $class($from, $prefix, $bitfield);
    }

    function __toString()
    {
        $flags = array_keys($this->getFlags(true));
        $subPrefix = function ($flag) { return substr($flag, strlen($this->prefix)); };

        return sprintf(
            '[bin: %b] [dec: %s] [%s: %s]',
            $this->bitfield,
            $this->bitfield,
            $this->prefix ? $this->prefix.'*': 'flags',
            implode(' | ', $this->prefix ? array_map($subPrefix, $flags) : $flags)
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
