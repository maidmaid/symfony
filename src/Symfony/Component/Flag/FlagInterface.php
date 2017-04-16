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
    public function __construct($from, $prefix);
    public function __toString();
    public function set($flags);
    public function get();
    public function add($flag);
    public function remove($flag);
    public function has($flag);
}
