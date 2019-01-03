<?php
declare(strict_types=1);

namespace PhpDoc2Rst\Util;

use ProgressBar\Manager;

class ProgressBar
{
    /**
     * @var int
     */
    private const INTERVAL_PERCENT = 10;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var int
     */
    private $total_cnt;

    /**
     * @var int
     */
    private $proceeded_cnt = 0;

    /**
     * @var int
     */
    private $next_indicated_percent = 0;

    /**
     * @param int $total_cnt
     */
    public function __construct(int $total_cnt)
    {
        $this->total_cnt = $total_cnt;
        $this->manager = new Manager(0, $total_cnt, 50, '█', ' ', '▋');
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function advance(): void
    {
        $this->proceeded_cnt += 1;

        $calculated_percent = 100 * $this->proceeded_cnt / $this->total_cnt;
        if ($calculated_percent >= $this->next_indicated_percent) {
            $this->manager->update($this->proceeded_cnt);
            $this->next_indicated_percent += self::INTERVAL_PERCENT;
        }
    }
}
