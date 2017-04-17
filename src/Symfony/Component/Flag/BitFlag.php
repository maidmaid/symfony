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
    public function set($mask)
    {
        // TODO throw InvalidArgumentException if !is_int
        // TODO throw InvalidArgumentException if > FLAG_MAX_VALUE

        $this->mask = $mask;

        return $this;
    }

    public function add($flag)
    {
        $this->mask |= $flag;

        return $this;
    }

    public function remove($flag)
    {
        $this->mask &= ~$flag;

        return $this;
    }

    public function has($flags)
    {
        return ($this->mask & $flags) === $flags;
    }
}
