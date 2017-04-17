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

class BinarizedFlag extends Flag
{
    private $indexed = null;
    private $binarized = array();

    private function binarize($flag)
    {
        if (null === $this->indexed) {
            $this->indexed = array_flip(array_values($this->getFlags()));
        }

        if (!isset($this->binarized[$flag])) {
            $this->binarized[$flag] = 1 << $this->indexed[$flag];
        }

        return $this->binarized[$flag];
    }

    public function add($flag)
    {
        if (false === $this->from && !isset($this->flags[$flag])) {
            $this->flags[$flag] = $flag;
            $this->indexed[$flag] = count($this->indexed);
        }

        $this->bitfield |= $this->binarize($flag);

        return $this;
    }

    public function remove($flag)
    {
        return parent::remove($this->binarize($flag));
    }

    public function has($flags)
    {
        return parent::has($this->binarize($flags));
    }
}
