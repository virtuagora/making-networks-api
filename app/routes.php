<?php

$app->get('/', function ($request, $response, $args) {
    $this->logger->info('Hello!');
    return $response->withJSON([
        'name' => 'hello!',
        'sub' => $request->getAttribute('subject'),
    ]);
})->setName('showHome');

$app->get('/install', function($request, $response, $params) {
    $env = $this->settings['env'] ?? 'pro';
    $installer = new \App\Migration\Release000Migration($this->db);
    if ($installer->isInstalled() && $env == 'pro') {
        return $response->withJSON(['mensaje' => 'La instalación ha fallado']);
    }
    $installer->down();
    $installer->up();
    $installer->populate();
    $installer->updateActions();
    return $response->withJSON(['message' => 'instalación exitosa']);
});

$app->get('/test', function ($req, $res, $arg) {
    return $res->withJSON([
        'sub' => $this->session->authenticate($req)->toArray()
    ]);
    //return $res->withJSON($this->session->get('user'));
});

$app->group('/v1', function () {
    $this->post('/tokens', 'sessionApiGate:createSession');
    $this->post('/pending-users', 'userApiGate:createPendingUser')->setName('apiC1PendingUser');
    $this->post('/users', 'userApiGate:createUser')->setName('apiC1User');
    $this->get('/users/{usr}', 'userApiGate:retrieveUser')->setName('apiR1User');
    // $this->get('/place', 'placeApiGate:getMultiPlace')->setName('apiGetMultiPlace');
    // $this->post('/place/{pla}/vote', 'placeApiGate:postOneVote');
});

//$app->get('/send-mail', 'App\ExampleController:sendMail');

//$app->get('/query-db', 'App\ExampleController:queryDB');