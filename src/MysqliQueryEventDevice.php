<?php
/*
 * Copyright 2015 Stephen Coakley <me@stephencoakley.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy
 * of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace Evflow;

class MysqliQueryWatcher extends Watcher
{
    protected $link;

    public function __construct(\mysqli $link, callable $callback, LoopInterface $loop = null)
    {
        parent::__construct($callback, $loop);
        $this->link = $link;
    }

    public function getResult()
    {
        return $this->link->reap_async_query();
    }

    public function poll()
    {
        $links = [$this->c];

        return mysqli_poll($links, $links, $links, 0) > 0;
    }

    public function await()
    {
        while (true) {
            $links = [$this->c];
            if (mysqli_poll($links, $links, $links, 1000) > 0) {
                return $this->getResult();
            }
        }
    }
}
