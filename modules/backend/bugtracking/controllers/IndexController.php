<?php

namespace ZCMS\Backend\Bugtracking\Controllers;

use ZCMS\Core\Models\UserRoles;
use ZCMS\Core\ZAdminController;
use ZCMS\Core\ZPagination;
use ZCMS\Core\ZMobileDetect;
use Phalcon\Forms\Element\Select;
use ZCMS\Core\Models\BugTracking;
use ZCMS\Core\Models\BugTrackingType;
use ZCMS\Core\Models\BugTrackingStatus;
use ZCMS\Core\Models\BugTrackingPriority;
use ZCMS\Backend\Bugtracking\Forms\BugTrackingForm;

/**
 * Class IndexController
 *
 * @package ZCMS\Backend\Bugtracking
 */
class IndexController extends ZAdminController
{
    const LINK_VIEW_DETAIL = '/admin/bugtracking/index/viewdetail/';

    /**
     * View location
     *
     * @var string
     */
    public $linkView = '/admin/bugtracking/index/view/';

    /**
     * View detail
     *
     * @param int $id
     */
    public function viewDetailAction($id)
    {

        $this->_toolbar->addBreadcrumb(['title' => 'Bug Tracking']);
        $this->_toolbar->addBreadcrumb(['title' => 'Detail']);
        $this->_toolbar->addHeaderPrimary('Detail Bug #' . intval($id));
        $this->viewAction($id);
    }

    /**
     * Get items
     *
     * @param int $id
     * @return array
     */
    private function getItem($id)
    {
        $query = $this->modelsManager->createBuilder()
            ->columns("b.id as bid,
            b.image,
            b.bug_tracking_status_id,
            bts.name as bug_tracking_status,
            CONCAT(u.first_name, ' ', u.last_name) as full_name,
            b.description as description,
            b.created_at as b_created_at,
            ur.created_at as user_role_created_at,
            ur.name as name,
            b.created_by,
            b.role_id,
            btt.name as btt_name,
            btp.name as btp_name ")
            ->addFrom('\ZCMS\Core\Models\BugTracking', 'b')
            ->join('\ZCMS\Core\Models\UserRole', 'b.role_id = ur.id', 'ur')
            ->join('\ZCMS\Core\Models\BugTrackingPriority', 'b.bug_tracking_priority_id = btp.id', 'btp')
            ->join('\ZCMS\Core\Models\BugTrackingType', 'b.bug_tracking_type_id = btt.id', 'btt')
            ->join('\ZCMS\Core\Models\BugTrackingStatus', 'b.bug_tracking_status_id = bts.id', 'bts')
            ->join('\ZCMS\Core\Models\Users', 'b.created_by = u.id', 'u');

        if ($this->_user['is_super_admin'] || $this->acl->isAllowed('bugtracking|index|viewAllNotify')) {
            $query->where('b.id = ' . $id);
        } else {
            $query->where('b.id = ' . $id . ' AND (b.created_by = ' . $this->_user['id'] . ' OR b.role_id = ' . $this->_user['role'] . ')');
        }
        return $query->getQuery()->execute()->toArray();
    }

    /**
     * View detail exec
     *
     * @param int $id
     */
    public function viewAction($id)
    {
        $id = intval($id);

        $item = $this->getItem($id);

        if (count($item) > 0) {
            $item = $item[0];
            if ($item['created_by'] == $this->_user['id'] || $item['role_id'] == $this->_user['role'] || $this->acl->isAllowed('bugtracking|index|viewAllNotify') || $this->_user['is_super_admin']) {
                $this->view->setVar('item', $item);
                $select = new Select('bug_tracking_status_id', BugTrackingStatus::find(['order' => 'ordering']), [
                    'using' => ['id', 'name'],
                    'useEmpty' => true,
                    'emptyText' => '-- Select --',
                    'class' => 'form-control',
                    'value' => $item['bug_tracking_status_id']
                ]);
                $select = $select->render();
                $this->view->setVar('bug_tracking_status', $select);
                if ($item['created_by'] == $this->_user['id']) {
                    $this->view->setVar('accessEdit', '1');
                }
            }
        }
        $this->view->setVar('linkDetail', self::LINK_VIEW_DETAIL);
    }

    /**
     * Change status tracking with ID
     *
     * @param $id
     */
    public function changeStatusTrackingAction($id)
    {
        if ($this->request->isAjax()) {
            $id = intval($id);
            $item = $this->getItem($id);

            if (count($item) == 1) {
                $item = $item[0];
                if ($item['created_by'] == $this->_user['id'] || $item['role_id'] == $this->_user['role'] || $this->acl->isAllowed('bugtracking|index|viewAllNotify') || $this->_user['is_super_admin']) {
                    /**
                     * @var BugTracking $bugTracking
                     */
                    $bugTracking = BugTracking::findFirst([
                        'conditions' => 'id = ?0',
                        'bind' => [$id]
                    ]);
                    $status = $this->request->getPost('bug_tracking_status_id', 'int', 0);
                    $checkStatus = BugTrackingStatus::findFirst([
                        'conditions' => 'id = ?0',
                        'bind' => [$status]
                    ]);
                    if ($checkStatus) {
                        $bugTracking->bug_tracking_status_id = $status;
                        if ($bugTracking->save()) {
                            die('1');
                        }
                    }
                }
            }
        }
        die('0');
    }

    /**
     * Over view bug tracking and request
     */
    public function indexAction()
    {

        $this->_toolbar->addBreadcrumb(['title' => 'Bug Tracking']);
        $this->_toolbar->addBreadcrumb(['title' => 'Over view']);
        $this->_toolbar->addHeaderPrimary('Bug Tracking and Feature Request');
        $this->_toolbar->addHeaderSecond('Overview');
        $this->_toolbar->addNewButton('bugtracking|index|new', '/admin/bugtracking/index/new/', 'New Bug / Request');

        $this->view->setVar('bug', $this->getNewTracking(1));
        $this->view->setVar('request', $this->getNewTracking(2));
    }

    /**
     * Get view detect
     */
    private function getViewDetect()
    {
        $zMobileDetect = new ZMobileDetect();
        if (!$zMobileDetect->isTablet() && $zMobileDetect->isMobile()) {
            $this->view->setVar('isMobile', 1);
            $this->linkView = self::LINK_VIEW_DETAIL;
        } else {
            $this->view->setVar('isMobile', 0);
        }
    }

    /**
     * Display current user bug
     */
    public function myBugAndRequestAction()
    {
        $this->_toolbar->addBreadcrumb(['title' => 'Bug tracking']);
        $this->_toolbar->addBreadcrumb(['title' => 'My Bug / Request']);
        $this->_toolbar->addHeaderPrimary('All tracking created by you');
        $this->_toolbar->addHeaderSecond('My Bug / Request');
        $this->_toolbar->addNewButton('bugtracking|index|new', '/admin/bugtracking/index/new/', 'New Bug / Request');

        $this->addFilterInList();

        $filter = $this->getFilter();

        $conditions = $this->getConditions($filter);

        //View notify create by Current user OR notify to Current User Role
        $conditions[] = '(b.created_by = ' . $this->_user['id'] . ')';

        $condition = implode(' AND ', $conditions);

        $this->setItemsToView($condition, $filter);

        //Set column name, value
        $_pageLayout = [
            [
                'type' => 'check_all',
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'link',
                'title' => 'Description',
                'class' => 'view-bug-tracking',
                'access' => $this->acl->isAllowed('bugtracking|index|view'),
                'link' => $this->linkView,
                'column' => 'description',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_description'
                ]
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Type',
            'column' => 'btt_name',
            'css' => 'width: 110px;',
            'label' => [
                [
                    'condition' => '==',
                    'condition_value' => 'Feature Request',
                    'class' => 'label z-label label-sm label-success',
                    'text' => 'Request'
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'Bug Tracking',
                    'class' => 'label z-label label-sm label-warning',
                    'text' => 'Bug'
                ]
            ],
            'class' => 'text-center',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_type',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_type'] == '' ? -1 : $filter['filter_bug_tracking_type'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingType::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Priority',
            'column' => 'btp_name',
            'class' => 'text-center',
            'css' => 'width: 120px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_priority',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_priority'] == '' ? -1 : $filter['filter_bug_tracking_priority'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingPriority::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Notify To',
            'column' => 'name',
            'class' => 'text-center',
            'css' => 'width: 160px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_user_role',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_user_role'] == '' ? -1 : $filter['filter_user_role'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => UserRoles::find([
                    'conditions' => 'location = 1',
                    'oder' => 'name ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'date',
            'title' => 'gb_created_at',
            'column' => 'b_created_at',
            'filter' => [
                'type' => 'dateRange',
                'name' => 'filter_created_at',
                'attributes' => []
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Status',
            'label' => [
                [
                    'condition' => '==',
                    'condition_value' => 'Done',
                    'class' => 'label z-label label-sm label-success',
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'Waiting Process',
                    'class' => 'label z-label label-default',
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'In Progress',
                    'class' => 'label z-label label-info',
                ],
            ],
            'css' => 'width: 120px',
            'class' => 'text-center',
            'column' => 'bug_tracking_status',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_status',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_status'] == '' ? -1 : $filter['filter_bug_tracking_status'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingStatus::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'id',
            'title' => 'ID',
            'column' => 'id',
            'css' => 'width: 70px',
            'filter' => [
                'type' => 'text',
                'name' => 'filter_id'
            ]
        ];

        $this->view->setVar('_pageLayout', $_pageLayout);
    }

    /**
     * List all bug
     */
    public function listAction()
    {
        $this->_toolbar->addBreadcrumb(['title' => 'Bug Tracking']);
        $this->_toolbar->addBreadcrumb(['title' => 'View All']);
        $this->_toolbar->addHeaderPrimary('View All');
        $this->_toolbar->addHeaderSecond('Bug Tracking');
        $this->_toolbar->addNewButton('bugtracking|index|new', '/admin/bugtracking/index/new/', 'New Bug / Request');

        $this->addFilterInList();

        $filter = $this->getFilter();

        $conditions = $this->getConditions($filter);

        $condition = implode(' AND ', $conditions);

        $this->setItemsToView($condition, $filter);

        //Set column name, value
        $_pageLayout = [
            [
                'type' => 'check_all',
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'link',
                'class' => 'view-bug-tracking',
                'title' => 'Description',
                'access' => $this->acl->isAllowed('bugtracking|index|view'),
                'link' => $this->linkView,
                'column' => 'description',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_description',
                ]
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Type',
            'label' => [
                [
                    'condition' => '==',
                    'condition_value' => 'Feature Request',
                    'class' => 'label z-label label-sm label-success',
                    'text' => 'Request'
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'Bug Tracking',
                    'class' => 'label z-label label-sm label-warning',
                    'text' => 'Bug'
                ]
            ],
            'column' => 'btt_name',
            'class' => 'text-center',
            'css' => 'width: 110px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_type',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_type'] == '' ? -1 : $filter['filter_bug_tracking_type'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingType::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Priority',
            'column' => 'btp_name',
            'class' => 'text-center',
            'css' => 'width: 120px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_priority',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_priority'] == '' ? -1 : $filter['filter_bug_tracking_priority'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingPriority::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Notify To',
            'column' => 'name',
            'class' => 'text-center',
            'css' => 'width: 160px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_user_role',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_user_role'] == '' ? -1 : $filter['filter_user_role'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => UserRoles::find([
                    'conditions' => 'location = 1',
                    'oder' => 'name ASC'
                ])
            ]
        ];


        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Submitted By',
            'column' => 'full_name',
            'class' => 'text-center'
        ];

        $_pageLayout[] = [
            'type' => 'date',
            'title' => 'gb_created_at',
            'column' => 'b_created_at',
            'filter' => [
                'type' => 'dateRange',
                'name' => 'filter_created_at',
                'attributes' => []
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Status',
            'css' => 'width: 120px',
            'class' => 'text-center',
            'label' => [
                [
                    'condition' => '==',
                    'condition_value' => 'Done',
                    'class' => 'label z-label label-sm label-success',
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'Waiting Process',
                    'class' => 'label z-label label-default',
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'In Progress',
                    'class' => 'label z-label label-info',
                ],
            ],
            'column' => 'bug_tracking_status',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_status',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_status'] == '' ? -1 : $filter['filter_bug_tracking_status'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingStatus::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'id',
            'title' => 'ID',
            'column' => 'id',
            'css' => 'width: 70px',
            'filter' => [
                'type' => 'text',
                'name' => 'filter_id'
            ]
        ];

        $this->view->setVar('_pageLayout', $_pageLayout);
    }

    /**
     * @param $id
     * @return \Phalcon\Http\ResponseInterface|bool
     */
    public function editAction($id)
    {
        /**
         * @var BugTracking $bugTracking
         */
        $bugTracking = BugTracking::findFirst([
            'conditions' => 'id = ?0',
            'bind' => [intval($id)]
        ]);

        if ($bugTracking && $this->_user['id'] == $bugTracking->created_by) {

            $this->_toolbar->addBreadcrumb(['title' => 'Bug Tracking']);
            $this->_toolbar->addBreadcrumb(['title' => 'Add New']);
            $this->_toolbar->addHeaderPrimary('Add New Bug / Request');
            $this->_toolbar->addHeaderSecond('Bug Tracking');
            $this->_toolbar->addSaveButton('bugtracking|index|edit', '/admin/bugtracking/index/edit/');
            $this->_toolbar->addCancelButton('bugtracking|index|index', '/admin/bugtracking/index/index/');

            $form = new BugTrackingForm($bugTracking);
            if ($bugTracking->image != '') {
                $this->view->setVar('imageBug', $bugTracking->image . '?time=' . time());
            }

            if ($this->request->isPost()) {

                if ($form->isValid($_POST, $bugTracking)) {
                    if ($bugTracking->save()) {
                        $OK = $this->saveImageBugTracking($bugTracking->id);
                        if ($OK === false) {
                            $this->flashSession->notice('Change image error!');
                        } else {
                            if ($OK !== true) {
                                $bugTracking->image = $OK;
                                $bugTracking->save();
                            }
                            $this->flashSession->success('Update bug tracking successfully!');
                        }
                        return $this->response->redirect('/admin/bugtracking/index/edit/' . $bugTracking->id . '/');
                    } else {
                        $this->setFlashSession($bugTracking->getMessages(), 'notice');
                    }
                } else {
                    $this->setFlashSession($form->getMessages(), 'notice');
                }
            }
            $this->view->setVar('form', $form);
            $this->view->pick('index/new');
            return true;
        } else {
            return $this->response->redirect('/admin/bugtracking/');
        }
    }

    /**
     * Add new bug tracking
     */
    public function newAction()
    {

        $this->_toolbar->addBreadcrumb(['title' => 'Bug Tracking']);
        $this->_toolbar->addBreadcrumb(['title' => 'Add New']);
        $this->_toolbar->addHeaderPrimary('Add New Bug / Request');
        $this->_toolbar->addHeaderSecond('Bug Tracking');
        $this->_toolbar->addSaveButton('bugtracking|index|new', '/admin/bugtracking/index/new/', 'Add');

        $form = new BugTrackingForm();
        $bugTracking = new BugTracking();

        if ($this->request->isPost()) {

            if ($form->isValid($_POST, $bugTracking)) {
                if ($bugTracking->save()) {
                    $OK = $this->saveImageBugTracking($bugTracking->id);
                    if ($OK === false) {
                        //$bugTracking->delete();
                    } else {
                        if ($OK !== true) {
                            $bugTracking->image = $OK;
                            $bugTracking->save();
                        }
                    }
                    $this->flashSession->success('Add new bug tracking successfully!');
                    return $this->response->redirect('/admin/bugtracking/index/edit/' . $bugTracking->id . '/');
                } else {
                    $this->setFlashSession($bugTracking->getMessages(), 'notice');
                }
            } else {
                $this->setFlashSession($form->getMessages(), 'notice');
            }
        }
        $this->view->setVar('form', $form);
        return false;
    }

    /**
     * Upload Image
     *
     * @param $idBugTracking
     * @return bool|string
     */
    private function saveImageBugTracking($idBugTracking)
    {
        /**
         * @var \Phalcon\Http\Request\File[] $files
         */
        $files = $this->request->getUploadedFiles();
        if (count($files)) {
            foreach ($files as $file) {
                $file_name = $file->getName();
                $file_size = $file->getSize();
                $file_type = $file->getRealType();
                $extension = '.' . pathinfo($file_name)['extension'];

                //Check file type
                if (substr($file_type, 0, 5) != 'image') {
                    $this->flashSession->notice('File upload not a image');
                    return false;
                }

                $newFile = $idBugTracking . $extension;
                //Check file size
                if ($file_size > BugTracking::IMAGE_MAX_FILE_SIZE * 1024 * 1024) {
                    $this->flashSession->notice('Image bug tracking > ' . BugTracking::IMAGE_MAX_FILE_SIZE . 'MB');
                    return false;
                }

                $folderImage = ROOT_PATH . '/public/' . BugTracking::IMAGE_BUG_TRACKING_FOLDER . $idBugTracking;
                if (!is_dir($folderImage)) {
                    mkdir($folderImage, 0755, true);
                }

                if ($file->moveTo($folderImage . '/' . $newFile)) {
                    return '/' . BugTracking::IMAGE_BUG_TRACKING_FOLDER . $idBugTracking . '/' . $newFile;
                }
            }

        }
        return true;
    }

    /**
     * Add filter
     */
    private function addFilterInList()
    {
        $this->getViewDetect();
        //Add sorting
        $this->addFilter('filter_order', 'id', 'string');
        $this->addFilter('filter_order_dir', 'DESC', 'string');

        //Add filter
        $this->addFilter('filter_bug_tracking_type', '', 'string');
        $this->addFilter('filter_bug_tracking_status', '', 'string');
        $this->addFilter('filter_bug_tracking_priority', '', 'string');
        $this->addFilter('filter_description', '', 'string');
        $this->addFilter('filter_created_at_from', '', 'string');
        $this->addFilter('filter_created_at_to', '', 'string');
        $this->addFilter('filter_user_role', '', 'string');
    }

    /**
     * Get conditions
     *
     * @param $filter
     * @return array
     */
    private function getConditions($filter)
    {
        //Set conditions
        $conditions = [];
        if ($filter['filter_created_at_from'] != '') {
            $conditions[] = "b.created_at >= '" . change_date_format($filter['filter_created_at_from'], "d-m-Y", "Y-m-d") . "'";
        }
        if ($filter['filter_created_at_to'] != '') {
            $conditions[] = "b.created_at <= '" . change_date_format($filter['filter_created_at_to'], "d-m-Y", "Y-m-d") . "'";
        }
        if ($filter['filter_user_role']) {
            $conditions[] = "b.role_id = " . intval($filter['filter_user_role']);
        }

        if ($filter['filter_bug_tracking_priority']) {
            $conditions[] = "b.bug_tracking_priority_id = " . intval($filter['filter_bug_tracking_priority']);
        }

        if ($filter['filter_description']) {
            $filter['filter_description'] = htmlspecialchars($filter['filter_description']);
            $conditions[] = "b.description LIKE '%" . $filter['filter_description'] . "%'";
        }

        if ($filter['filter_bug_tracking_type']) {
            $conditions[] = "b.bug_tracking_type_id = " . intval($filter['filter_bug_tracking_type']);
        }

        if ($filter['filter_bug_tracking_status']) {
            $conditions[] = "b.bug_tracking_status_id = '" . $filter['filter_bug_tracking_status'] . "'";
        }

        if ($this->_user['is_super_admin'] || $this->acl->isAllowed('bugtracking|index|viewAllNotify')) {

        } else {
            $conditions[] = '(b.created_by = ' . $this->_user['id'] . ' OR b.role_id = ' . $this->_user['role'] . ')';
        }
        return $conditions;
    }

    /**
     * Set item to view
     *
     * @param string $condition
     * @param array $filter
     */
    private function setItemsToView($condition, $filter)
    {
        //Limit
        $paginationLimit = $this->config->pagination->limit;
        $currentPage = $this->request->getQuery('page', 'int');

        $items = $this->modelsManager->createBuilder()
            ->columns("b.id as id, bts.name as bug_tracking_status, CONCAT(u.first_name, ' ', u.last_name) as full_name, zcms_cut_string(b.description, 63) as description, b.created_at as b_created_at, ur.created_at as user_role_created_at, ur.name as name, btt.name as btt_name, btp.name as btp_name ")
            ->addFrom('\ZCMS\Core\Models\BugTracking', 'b')
            ->join('\ZCMS\Core\Models\UserRoles', 'b.role_id = ur.id', 'ur')
            ->join('\ZCMS\Core\Models\BugTrackingPriority', 'b.bug_tracking_priority_id = btp.id', 'btp')
            ->join('\ZCMS\Core\Models\BugTrackingType', 'b.bug_tracking_type_id = btt.id', 'btt')
            ->join('\ZCMS\Core\Models\BugTrackingStatus', 'b.bug_tracking_status_id = bts.id', 'bts')
            ->join('\ZCMS\Core\Models\Users', 'b.created_by = u.id', 'u')
            ->where($condition)
            ->orderBy($filter['filter_order'] . ' ' . $filter['filter_order_dir']);

        $this->view->setVar('_page', ZPagination::getPaginationQueryBuilder($items, $paginationLimit, $currentPage));

        //Set search value
        $this->view->setVar('_filter', $filter);
    }

    /**
     * List all bug notify to me
     */
    public function bugAndRequestNotifyToMeAction()
    {
        $this->_toolbar->addBreadcrumb(['title' => 'Bug tracking']);
        $this->_toolbar->addBreadcrumb(['title' => 'Notify To Me']);
        $this->_toolbar->addHeaderPrimary('Tracking notify to your group');
        $this->_toolbar->addHeaderSecond('Notify To Me');
        $this->_toolbar->addNewButton('bugtracking|index|new', '/admin/bugtracking/index/new/', 'New Bug / Request');

        $this->addFilterInList();

        $filter = $this->getFilter();

        $conditions = $this->getConditions($filter);

        //View notify create by Current user OR notify to Current User Role
        $conditions[] = '(b.role_id = ' . $this->_user['role'] . ')';

        $condition = implode(' AND ', $conditions);

        $this->setItemsToView($condition, $filter);

        //Set column name, value
        $_pageLayout = [
            [
                'type' => 'check_all',
            ],
            [
                'type' => 'index',
                'title' => '#'
            ],
            [
                'type' => 'link',
                'title' => 'Description',
                'class' => 'view-bug-tracking',
                'access' => $this->acl->isAllowed('bugtracking|index|view'),
                'link' => $this->linkView,
                'column' => 'description',
                'filter' => [
                    'type' => 'text',
                    'name' => 'filter_description'
                ]
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Type',
            'label' => [
                [
                    'condition' => '==',
                    'condition_value' => 'Feature Request',
                    'class' => 'label z-label label-sm label-success',
                    'text' => 'Request'
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'Bug Tracking',
                    'class' => 'label z-label label-sm label-warning',
                    'text' => 'Bug'
                ]
            ],
            'column' => 'btt_name',
            'class' => 'text-center',
            'css' => 'width: 110px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_type',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_type'] == '' ? -1 : $filter['filter_bug_tracking_type'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingType::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Priority',
            'column' => 'btp_name',
            'class' => 'text-center',
            'css' => 'width: 120px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_priority',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_priority'] == '' ? -1 : $filter['filter_bug_tracking_priority'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingPriority::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Notify To',
            'column' => 'name',
            'class' => 'text-center',
            'css' => 'width: 160px;',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_user_role',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_user_role'] == '' ? -1 : $filter['filter_user_role'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => UserRoles::find([
                    'conditions' => 'location = 1',
                    'oder' => 'name ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Submitted By',
            'column' => 'full_name',
            'class' => 'text-center'
        ];

        $_pageLayout[] = [
            'type' => 'date',
            'title' => 'gb_created_at',
            'column' => 'b_created_at',
            'filter' => [
                'type' => 'dateRange',
                'name' => 'filter_created_at',
                'attributes' => []
            ]
        ];

        $_pageLayout[] = [
            'type' => 'text',
            'title' => 'Status',
            'label' => [
                [
                    'condition' => '==',
                    'condition_value' => 'Done',
                    'class' => 'label z-label label-sm label-success',
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'Waiting Process',
                    'class' => 'label z-label label-default',
                ],
                [
                    'condition' => '==',
                    'condition_value' => 'In Progress',
                    'class' => 'label z-label label-info',
                ],
            ],
            'css' => 'width: 120px',
            'class' => 'text-center',
            'column' => 'bug_tracking_status',
            'filter' => [
                'type' => 'select',
                'name' => 'filter_bug_tracking_status',
                'attributes' => [
                    'useEmpty' => true,
                    'emptyText' => 'All',
                    'value' => $filter['filter_bug_tracking_status'] == '' ? -1 : $filter['filter_bug_tracking_status'],
                    'using' => [
                        'id', 'name'
                    ]
                ],
                'value' => BugTrackingStatus::find([
                    'oder' => 'ordering ASC'
                ])
            ]
        ];

        $_pageLayout[] = [
            'type' => 'id',
            'title' => 'ID',
            'column' => 'id',
            'css' => 'width: 70px',
            'filter' => [
                'type' => 'text',
                'name' => 'filter_id'
            ]
        ];

        $this->view->setVar('_pageLayout', $_pageLayout);
    }

    /**
     * Get new bug tracking
     *
     * @param int $typeID
     * @return mixed
     */
    private function getNewTracking($typeID)
    {
        $where[] = 'b.bug_tracking_type_id =' . $typeID;
        if ($this->_user['is_super_admin'] || $this->acl->isAllowed('bugtracking|index|viewAllNotify')) {

        } else {
            $where[] = '(b.created_by = ' . $this->_user['id'] . ' OR b.role_id = ' . $this->_user['role'] . ')';
        }
        $where = implode(' AND ', $where);

        /**
         * @var $items
         */
        $items = $this->modelsManager->createBuilder()
            ->columns("b.id as id,
            bts.name as bug_tracking_status,
            CONCAT(u.first_name, ' ', u.last_name) as full_name,
            zcms_cut_string(b.description, 63) as description,
            b.created_at as b_created_at,
            ur.created_at as user_role_created_at,
            ur.name as name,
            btt.name as btt_name,
            btp.name as btp_name ")
            ->addFrom('\ZCMS\Core\Models\BugTracking', 'b')
            ->join('\ZCMS\Core\Models\UserRoles', 'b.role_id = ur.id', 'ur')
            ->join('\ZCMS\Core\Models\BugTrackingPriority', 'b.bug_tracking_priority_id = btp.id', 'btp')
            ->join('\ZCMS\Core\Models\BugTrackingType', 'b.bug_tracking_type_id = btt.id', 'btt')
            ->join('\ZCMS\Core\Models\BugTrackingStatus', 'b.bug_tracking_status_id = bts.id', 'bts')
            ->join('\ZCMS\Core\Models\Users', 'b.created_by = u.id', 'u')
            ->where($where)
            ->orderBy('b.created_at DESC')
            ->limit(10);
        return $items->getQuery()->execute();
    }
}