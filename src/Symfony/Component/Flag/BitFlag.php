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
    const FLAG_MAX_VALUE = 2147483647; // 2^31−1.

    function __toString()
    {
        $str = sprintf('0b%b [dec: %s]', $this->flags, $this->flags);

        if ($constants = $this->getConstants(true)) {
            $str .= sprintf(' [const: %s]', implode(' | ', array_keys($constants)));
        }

        return $str;
    }

    public function set($flags)
    {
        // TODO throw InvalidArgumentException if !is_int
        // TODO throw InvalidArgumentException if > FLAG_MAX_VALUE

        $this->flags = $flags;

        return $this;
    }

    public function add($flag)
    {
        $this->flags |= $flag;

        return $this;
    }

    public function remove($flag)
    {
        $this->flags &= ~$flag;

        return $this;
    }

    public function has($flags)
    {
        return ($this->flags & $flags) === $flags;
    }
}
