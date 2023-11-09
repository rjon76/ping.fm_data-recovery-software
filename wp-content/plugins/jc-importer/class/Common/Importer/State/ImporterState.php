<?php

namespace ImportWP\Common\Importer\State;

use ImportWP\Common\Runner\RunnerState;

class ImporterState extends RunnerState
{
    protected static $object_type = 'importer';

    public function is_running()
    {
        return $this->has_status('running');
    }

    public function is_resumable()
    {
        return $this->has_section(['import', 'delete', 'timeout']);
    }

    protected function default($session)
    {
        return array_merge(parent::default($session), [
            'section' => 'import',
            'progress' => [
                'import' => [
                    'start' => 0,
                    'end' => 0,
                    'current_row' => 0,
                ],
                'delete' => [
                    'start' => 0,
                    'end' => 0,
                    'current_row' => 0,
                ]
            ],
        ]);
    }
}
