<?php

// Title
echo $HTML->title_panel([
    'heading' => $Lang->get('Delete Address')
], $CurrentUser);

echo $Form->form_start();
    echo $HTML->warning_message('Are you sure you wish to delete the address ‘%s’', $details['addressTitle']);
    echo $Form->hidden('addressID', $details['addressID']);
    echo $Form->submit_field('btnSubmit', 'Delete', $API->app_path());
echo $Form->form_end();
