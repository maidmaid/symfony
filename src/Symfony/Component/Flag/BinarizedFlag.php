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
 * Some flags have no-integer values like this:
 *
 * <code>
 * const METHOD_HEAD = 'HEAD';
 * const METHOD_GET = 'GET';
 * const METHOD_POST = 'POST';
 * const METHOD_PUT = 'PUT';
 * </code>
 *
 * This Flag class binarizes internaly no-integer values.
 *
 * @author Dany Maillard <danymaillard93b@gmail.com>
 */
class BinarizedFlag extends Flag
{
    private $map = array();
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
     * @param string $value No-integer value.
     *
     * @return int Binarized value.
     */
    private function binarize($value, $flag = null)
    {
        if (!isset($this->map[$value])) {
            $this->map[$value] = 1 << count($this->map);
            $this->binarized[$this->map[$value]] = null === $flag ? $value : $flag;
        }

        return $this->map[$value];
    }

    /**
     * {@inheritdoc}
     */
    public function add($flag)
    {
        if (false === $this->from && !isset($this->flags[$flag])) {
            $this->flags[$flag] = $flag;
        }

        $this->set($this->bitfield | $this->binarize($flag));

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

    /**
     * {@inheritdoc}
     */
    public function getIterator($flagged = false)
    {
        if (false !== $this->from && empty($this->binarized)) {
            foreach (parent::getIterator() as $value => $flag) {
                $this->binarize($value, $flag);
            }
        }

        return new \ArrayIterator($flagged
            ? array_filter($this->binarized, function ($v) { return parent::has($v); }, ARRAY_FILTER_USE_KEY)
            : $this->binarized
        );
    }
}
