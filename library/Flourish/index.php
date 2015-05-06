<?php

include($_SERVER['DOCUMENT_ROOT'] . '/flourish/init.php');

try {

$db  = new fDatabase('mssql', 'app', 'user', 'password', '192.168.0.17', '1433');

    $db->connect();

} catch (fAuthorizationException $e) {
    fMessaging::create('error', $e->getMessage());
} catch (fConnectivityException $e) {
    fMessaging::create('error', $e->getMessage());
}


$result = $db->translatedQuery('SELECT * FROM quote LIMIT 1');
foreach ($result as $row) {
    var_dump($row);
}

echo "\n\n<hr />\n\n";

fORMDatabase::attach($db);
fORM::mapClassToTable('Users', 'users');

$user = new Users(1);

echo "\n\n<hr />0\n\n";
var_dump($user);

$users = fRecordSet::build('Users');

echo "\n\n<hr />1\n\n";
var_dump($users);

$users = fRecordSet::buildFromSQL(
    'Users',
    "SELECT * FROM users LIMIT 2",
    "SELECT count(*) FROM users",
    2, // $limit
    1  // $page
);

echo "\n\n<hr />2\n\n";
var_dump($users);

try {
$user->setSalt('DTL');
$user->store();

echo "\n\n<hr />\n\n";
var_dump($user->getSalt());

    $user->store();

} catch (fExpectedException $e) {
    echo $e->printMessage();
}



//$users = fRecordSet::build('Users');

echo 'done';
