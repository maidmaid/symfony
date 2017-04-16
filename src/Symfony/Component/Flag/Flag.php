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

class Flag
{
    /**
     * @return FlagInterface
     */
    static public function create($from = false, $prefix = '', $hierarchical = false)
    {
        // TODO handle exceptions

        $isBit = true;
        if (false !== $from) {
            foreach (AbstractFlag::search($from, $prefix) as $value) {
                if (!is_int($value)) {
                    $isBit = false;
                    break;
                }
            }
        }

        switch (true) {
            case $isBit && !$hierarchical: $class = BitFlag::class; break;
            case $isBit && $hierarchical: $class = HierarchicalBitFlag::class; break;
            default: $class = ScalarFlag::class;
        }

        return new $class($from, $prefix);
    }
}
