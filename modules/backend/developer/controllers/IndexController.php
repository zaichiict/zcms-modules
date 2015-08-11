<?php

namespace ZCMS\Backend\Developer\Controllers;

use ZCMS\Core\ZAdminController;

/**
 * Class IndexController
 *
 * @package ZCMS\Backend\Developer\Controllers
 */
class IndexController extends ZAdminController
{

    /**
     * Index
     */
    public function indexAction()
    {
        $this->_toolbar->addBreadcrumb(['title' => 'm_admin_menu_developer']);
    }
}