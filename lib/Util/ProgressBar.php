<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use ProgressBar\Manager;

class ProgressBar
{
    /**
     * @var int
     */
    private const INTERVAL_PERC = 10;

    /**
     * @param int $total_php_cnt
     * @return Manager
     */
    public static function create($total_php_cnt): Manager
    {
        return new Manager(0, $total_php_cnt, 50, '█', ' ', '▋');
    }

    /**
     * @param int $total_cnt
     * @param int $current_cnt
     * @param Manager $manager
     * @param int $current_perc
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function updateProgressBar(int $total_cnt, int $current_cnt, Manager $manager, int $current_perc): int {
        if (($current_cnt/$total_cnt) >= $current_perc/100) {
            $manager->update($current_cnt);
            return ($current_perc + self::INTERVAL_PERC);
        }

        return $current_perc;
    }
}
