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

use Symfony\Component\Flag\Exception\InvalidArgumentException;

abstract class AbstractFlag implements FlagInterface
{
    protected $from;
    protected $prefix;
    protected $bitfield;
    protected $flags = null;

    public function __construct($from = false, $prefix = '', $bitfield = 0)
    {
        $this->from = $from;
        $this->prefix = $prefix;
        $this->set($bitfield);
    }

    /**
     * Creates dynamically instance of Flag.
     *
     * @param string|null|bool $from         Class from the search flags is made. Define to null to search flags in
     *                                       global space. Define to false for standalone use.
     * @param string           $prefix       Prefix flags from the search flags is made.
     * @param bool             $hierarchical Defines hierarchical flags.
     * @param int              $bitfield     Sets bitfield value.
     *
     * @return AbstractFlag
     *
     * @throws InvalidArgumentException When standalone use is defined as hierarchical.
     * @throws InvalidArgumentException When no-integer flags is defined as hierarchical.
     */
    static public function create($from = false, $prefix = '', $hierarchical = false, $bitfield = 0)
    {
        $onlyInt = true;
        $forceToBinarize = false;

        if (false === $from) {
            if ($hierarchical) {
                throw new InvalidArgumentException('Potential no-integer flags must not be hierarchical.');
            }
            $forceToBinarize = true;
        } else {
            foreach (self::search($from, $prefix) as $value) {
                if (!is_int($value)) {
                    $onlyInt = false;
                    break;
                }
            }
            if ($hierarchical && !$onlyInt) {
                throw new InvalidArgumentException('No-integer flags must not be hierarchical.');
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * Searchs flags from class or global space.
     *
     * @param null|string $from   Class from the search flags is made. Define to null to search flags in global space.
     * @param string      $prefix Prefix flags that filter search result.
     *
     * @return array Array of flags.
     */
    static public function search($from, $prefix = '')
    {
        if (null === $from && '' === $prefix) {
            throw new InvalidArgumentException('A prefix must be setted if searching is in global space.');
        }

        // TODO search in namespaced constants (get_defined_constants(true)['user'])
        $constants = null === $from
            ? get_defined_constants()
            : (new \ReflectionClass($from))->getConstants()
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
