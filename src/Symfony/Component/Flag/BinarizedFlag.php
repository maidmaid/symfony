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

/**
 * Concrete Flag class that handles no-integer values.
 *
 * Some flags have bitfields no-integer like this.
 *
 * <code>
 * const METHOD_HEAD = 'HEAD';
 * const METHOD_GET = 'GET';
 * const METHOD_POST = 'POST';
 * const METHOD_PUT = 'PUT';
 * </code>
 *
 * Internaly, this flag class binarizes no-integer values.
 *
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class BinarizedFlag extends Flag
{
    private $indexed = null;
    private $binarized = array();

    /**
     * Converts no-integer value flag in saved binary field.
     *
     * <code>
     * | Flag         | Value  | Index | Binary |
     * ------------------------------------------
     * | METHOD_HEAD  | 'HEAD' | 0     | 0b0001 |
     * | METHOD_GET   | 'GET'  | 1     | 0b0010 |
     * | METHOD_POST  | 'POST' | 2     | 0b0100 |
     * | METHOD_PUT   | 'PUT'  | 3     | 0b1000 |
     * </code>
     *
     * @param $flag
     * @return mixed
     */
    private function binarize($flag)
    {
        if (null === $this->indexed) {
            $this->indexed = array_flip(array_values($this->getFlags()));
        }

        if (!isset($this->indexed[$flag])) {
            $this->indexed[$flag] = count($this->indexed);
        }

        if (!isset($this->binarized[$flag])) {
            $this->binarized[$flag] = 1 << $this->indexed[$flag];
        }

        return $this->binarized[$flag];
    }

    /**
     * {@inheritdoc}
     */
    public function add($flag)
    {
        if (false === $this->from && !isset($this->flags[$flag])) {
            $this->flags[$flag] = $flag;
        }

        $this->bitfield |= $this->binarize($flag);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($flag)
    {
        return parent::remove($this->binarize($flag));
    }

    /**
     * {@inheritdoc}
     */
    public function has($flags)
    {
        return parent::has($this->binarize($flags));
    }
}
