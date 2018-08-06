<?php
namespace Core\Classes\Helpers;
use Core\Classes as CoreClasses;
use Core\Interfaces as CoreInterfaces;

abstract class AbstractHelper extends CoreClasses\CoreObject implements CoreInterfaces\Configurable {
    private $site;

    public function getSite() {
        return $this->site;
    }

    public function setSite($site) {
        $this->site = $site;
    }

    /**
    *   Override to handle any Helper specific configurations.
    */
    public function configure(CoreInterfaces\Site $site) {
        $this->setSite($site);
    }
}
