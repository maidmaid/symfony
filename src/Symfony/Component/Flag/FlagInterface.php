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

interface FlagInterface
{
    /**
     * Constructor.
     *
     * @param bool   $from     Class from the search flags is made.
     * @param string $prefix   Prefix flags.
     * @param int    $bitfield Bitfield value.
     */
    public function __construct($from = false, $prefix = '', $bitfield = 0);
    public function __toString();

    /**
     * Sets bitfield value.
     *
     * @param int $bitfield Bitfield value
     *
     * @return $this
     */
    public function set($bitfield);
    public function get();
    public function add($flag);
    public function remove($flag);
    public function has($flag);

    /**
     * Get flags.
     *
     * @param bool $flagged Filter for get only flagged flags.
     *
     * @return mixed
     */
    public function getFlags($flagged = false);
}
