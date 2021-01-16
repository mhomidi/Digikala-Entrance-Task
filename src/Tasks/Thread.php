<?php
/**
 * Created by PhpStorm.
 * User: hadi
 * Date: 1/16/21
 * Time: 3:02 PM
 */

namespace App\Tasks;

/**
 * Class Thread
 * @package App\Tasks
 * Because the extension of pthreads for php is not installed on my pc, I create this class to
 * use it when I want to use multi-threading. For make it correct, just after install extension
 * change the 'Thread' class in tasks classes.
 */

abstract class Thread
{
    abstract function run();

    public function start()
    {
        return $this->run();
    }
}