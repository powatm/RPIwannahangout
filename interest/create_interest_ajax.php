<?php
require_once '../database_access.php';

$ei = new EventInterest();
if(isset($_POST['interested_user_id'])) {
    $ei->setInterestedUserId($_POST['interested_user_id']);
}
if(isset($_POST['target_event_id'])) {
    $ei->setTargetEventId($_POST['target_event_id']);
}
if(isset($_POST['bringing_car'])) {
    $ei->setBringingCar($_POST['bringing_car']);
}

if (!$ei->validate()) {
    foreach ($ei->getValidationFailures() as $failure) {
        echo '<p><strong>Error in '.$failure->getPropertyPath().' field!</strong> '.$failure->getMessage().'</p>';
    }
    unset($failure);
}
else {
    //Check to see if this interest is already here.

    $eq = EventInterestQuery::create()
        ->filterByInterestedUserId($_POST['interested_user_id'])
        ->filterByTargetEventId($_POST['target_event_id'])
        ->count();
    if($eq != 0) {
        echo '{ "extant": 1 }';
    }
    else {
        $ei->save();
        // add the author name and return the JSON
        $ei_json = json_decode($ei->toJSON());
        $author = $ei->getInterested();
        $ei_json->authorFirstName = $author->getFirstName();
        $ei_json->authorLastName = $author->getLastName();
        echo json_encode($ei_json);
    }
}

?>