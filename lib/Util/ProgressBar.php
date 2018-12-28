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
     * @var Manager
     */
    private $manager;

    /**
     * @var int
     */
    private $total_php_cnt;

    /**
     * @var int
     */
    private $proceed_php_cnt = 0;

    /**
     * @var int
     */
    private $current_interval_perc = 0;

    /**
     * @param int $total_php_cnt
     */
    public function __construct($total_php_cnt)
    {
        $this->total_php_cnt = $total_php_cnt;
        $this->manager = new Manager(0, $total_php_cnt, 50, '█', ' ', '▋');
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function advance()
    {
        $current_perc = 100 * $this->proceed_php_cnt / $this->total_php_cnt;
        if ($current_perc >= $this->current_interval_perc) {
            $this->manager->update($this->proceed_php_cnt);
            $this->current_interval_perc += self::INTERVAL_PERC;
        }
        $this->proceed_php_cnt += 1;
    }
}
