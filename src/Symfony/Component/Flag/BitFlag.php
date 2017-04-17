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

class BitFlag extends AbstractFlag
{
    public function set($bitfield)
    {
        // TODO throw InvalidArgumentException if !is_int
        // TODO throw InvalidArgumentException if > FLAG_MAX_VALUE

        $this->bitfield = $bitfield;

        return $this;
    }

    public function add($flag)
    {
        if (false === $this->from && !isset($this->flags[$flag])) {
            $this->flags[$flag] = $flag;
        }

        $this->bitfield |= $flag;

        return $this;
    }

    public function remove($flag)
    {
        $this->bitfield &= ~$flag;

        return $this;
    }

    public function has($flags)
    {
        return ($this->bitfield & $flags) === $flags;
    }
}
