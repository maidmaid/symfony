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

class ScalarFlag extends AbstractFlag
{
    public function __construct($from = false, $prefix = '')
    {
        parent::__construct($from, $prefix);

        $this->flags = array();
    }

    public function __toString()
    {
        return implode(' | ', array_keys($this->getConstants(true) ?: $this->flags));
    }

    public function add($flag)
    {
        $this->flags[$flag] = true;

        return $this;
    }

    public function remove($flag)
    {
        if (isset($this->flags[$flag])) {
            unset($this->flags[$flag]);
        }

        return $this;
    }

    public function has($flag)
    {
        return isset($this->flags[$flag]);
    }
}
