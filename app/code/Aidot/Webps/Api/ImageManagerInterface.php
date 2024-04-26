<?php

namespace Aidot\Webps\Api;

interface ImageManagerInterface {
    /**
     * @return mixed
     */
    public function manager();

    /**
     * @return mixed
     */
    public function execute();

    /**
     * @return mixed
     */
    public function repair();
}