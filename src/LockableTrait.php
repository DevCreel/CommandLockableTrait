<?php

namespace DevCreel\Command;

/*
 * Copyright (c) 2017 Sergey Mazurak
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Filesystem\LockHandler;

trait LockableTrait
{
    private $lockHandler;

    /**
     * Locks a command.
     *
     * @param null $name
     * @param bool $blocking
     * @return bool
     */
    private function lock($name = null, $blocking = false)
    {
        if (!class_exists(LockHandler::class)) {
            throw new RuntimeException('To enable the locking feature you must install the symfony/filesystem component.');
        }

        if (null !== $this->lockHandler) {
            return false;
        }

        $this->lockHandler = new LockHandler($name ?: $this->getName());

        if (!$this->lockHandler->lock($blocking)) {
            $this->lockHandler = null;
            return false;
        }

        return true;
    }

    /**
     * Locks a command with threads.
     *
     * @param $countThreads
     * @param null $name
     * @return bool|int
     */
    private function lockThread($countThreads, $name = null)
    {
        $thread = false;

        for ($i = 1; $i <= $countThreads; $i++) {
            if (!$this->lock(($name ?: $this->getName()) . $i)) {
                continue;
            } else {
                $thread = $i;
            }
        }

        return $thread;
    }

    /**
     * Releases the command lock if there is one.
     */
    private function release()
    {
        if ($this->lockHandler) {
            $this->lockHandler->release();
            $this->lockHandler = null;
        }
    }

}