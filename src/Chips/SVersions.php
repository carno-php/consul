<?php
/**
 * Service versions (index)
 * User: moyo
 * Date: 22/09/2017
 * Time: 6:15 PM
 */

namespace Carno\Consul\Chips;

trait SVersions
{
    /**
     * @var int
     */
    private $versionIDX = null;

    /**
     * @return bool
     */
    public function hasVersion() : bool
    {
        return ! is_null($this->versionIDX);
    }

    /**
     * @param int $index
     * @return self
     */
    public function setVersion(int $index) : self
    {
        $this->versionIDX = $index;
        return $this;
    }

    /**
     * @return int
     */
    public function getVersion() : int
    {
        return $this->versionIDX ?? 0;
    }
}
