<?php

require_once '../vendor/autoload.php';
header('Content-Type: application/json');

$request = json_decode(file_get_contents('php://input'), true);

$id         = $request['id'] ?? null;
$firstname  = $request['firstname'] ?? null;
$lastname   = $request['lastname'] ?? null;
$nutename   = $request['nutename'] ?? null;
$birthday   = $request['birthday'] ?? null;
$birthmonth = $request['birthmonth'] ?? null;
$birthyear  = $request['birthyear'] ?? null;
$deathday   = $request['deathday'] ?? null;
$deathmonth = $request['deathmonth'] ?? null;
$deathyear  = $request['deathyear'] ?? null;
$father_id  = $request['father_id'] ?? null;
$mother_id  = $request['mother_id'] ?? null;

if(!isset($id) || $id == null)
{
    $response['status']    = 'error';
    $response['message']   = 'Please provide a valid person id';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

if(strlen($firstname) == 0) $firstname = null;
if(strlen($lastname) == 0) $lastname = null;
if(strlen($nutename) == 0) $nutename = null;
if(strlen($birthday) == 0) $birthday = null;
if(strlen($birthmonth) == 0) $birthmonth = null;
if(strlen($birthyear) == 0) $birthyear = null;
if(strlen($deathday) == 0) $deathday = null;
if(strlen($deathmonth) == 0) $deathmonth = null;
if(strlen($deathyear) == 0) $deathyear = null;
if(strlen($father_id) == 0) $father_id = null;
if(strlen($mother_id) == 0) $mother_id = null;

$person = FamArchive\Models\Person::getById($id);
if(!$person)
{
    $response['status']    = 'error';
    $response['message']   = 'Person not found';
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$person->setFirstName($firstname);
$person->setLastName($lastname);
$person->setNutName($nutename);
$person->setBirthYear($birthyear);
$person->setBirthMonth($birthmonth);
$person->setBirthDay($birthday);
$person->setDeathYear($deathyear);
$person->setDeathMonth($deathmonth);
$person->setDeathDay($deathday);
$person->save();

if($father_id || $mother_id)
{
    $personTree = new FamArchive\Models\PersonTree([
        'person_id' => $person->getId(),
        'mother_id' => $mother_id,
        'father_id' => $father_id
    ]);
    $personTree->save();
}

$response = [];
$response['status'] = 'success';
$response['message'] = 'Person updated successfully';
$response['data'] = $person->toArray();

echo json_encode($response, JSON_PRETTY_PRINT);
?>