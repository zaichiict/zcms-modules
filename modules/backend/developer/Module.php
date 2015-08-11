<?php

namespace ZCMS\Backend\Developer;

use ZCMS\Core\ZModule;

/**
 * Class Module
 *
 * @package ZCMS\Backend\Developer
 */
class Module extends ZModule
{
    /**
     * Define module name
     *
     * @var string
     */
    protected $module_name = 'developer';

    /**
     * Module Constructor
     */
    public function __construct()
    {
        parent::__construct($this->module_name);
    }
}
