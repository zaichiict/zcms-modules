<?php

namespace ZCMS\Backend\Bugtracking\Forms;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;
use ZCMS\Core\Forms\ZForm;
use ZCMS\Core\Models\BugTrackingPriority;
use ZCMS\Core\Models\BugTrackingType;
use ZCMS\Core\Models\UserRoles;

/**
 * Class BugTrackingForm
 *
 * @package ZCMS\Backend\Bugtracking\Forms
 */
class BugTrackingForm extends ZForm
{
    /**
     * Init form
     *
     * @param string $data
     */
    public function initialize($data = null)
    {
        $description = new TextArea('description', ['rows' => 5, 'required' => 'required']);
        $this->add($description);

        $bugTrackingType = new Select('bug_tracking_type_id',
            BugTrackingType::find([
                    'order' => 'ordering'
                ]
            ),
            [
                'using' => [
                    'id', 'name'
                ],
                'useEmpty' => true,
                'required' => 'required'
            ]);
        $bugTrackingType->addValidator(new PresenceOf([
            'message' => 'Type is required'
        ]));
        $this->add($bugTrackingType);

        $bugTrackingPriority = new Select('bug_tracking_priority_id',
            BugTrackingPriority::find([
                    'order' => 'ordering'
                ]
            ),
            [
                'using' => [
                    'id',
                    'name',

                ],
                'useEmpty' => true,
                'required' => 'required'
            ]);
        $bugTrackingPriority->addValidator(new PresenceOf([
            'message' => 'Priority is required'
        ]));
        $this->add($bugTrackingPriority);

        $notify = new Select('role_id',
            UserRoles::find([
                'conditions' => 'location = 1'
            ]),
            [
                'using' => [
                    'id',
                    'name'
                ],
                'useEmpty' => true,
                'required' => 'required'
            ]
        );
        $notify->addValidator(new PresenceOf([
            'message' => 'Notify is required'
        ]));
        $this->add($notify);
    }
}