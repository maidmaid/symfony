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

class Flag extends AbstractFlag
{
    public function set($bitfield)
    {
        if (PHP_INT_MAX < $bitfield) {
            throw new InvalidArgumentException('Bitfield must not exceed integer max limit.');
        }

        if (!is_int($bitfield)) {
            throw new InvalidArgumentException('Bitfield must be an integer.');
        }

        $this->bitfield = $bitfield;

        return $this;
    }

    public function add($flag)
    {
        // TODO throw InvalidArgumentException if max flags >= flag and has from

        if (false === $this->from && !isset($this->flags[$flag])) {
            $this->flags[$flag] = $flag;
        }

        $this->set($this->bitfield | $flag);

        return $this;
    }

    public function remove($flag)
    {
        $this->set($this->bitfield & ~$flag);

        return $this;
    }

    public function has($flags)
    {
        return ($this->bitfield & $flags) === $flags;
    }
}
