<?php

namespace App\Gate;

use App\Util\Utils;
use App\Util\ContainerClient;
use App\Util\Paginator;
use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;

class InitiativeApiGate extends AbstractApiGate
{
    protected $modelName = 'Initiative';
    protected $modelSlug = 'ini';

    // POST /initiatives
    public function createInitiative($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $data = $this->helper->getDataFromRequest($request);
        $init = $this->resources['initiative']->createInitiative($subject, $data);
        return $this->sendCreatedResponse($response, $init);
    }

    // GET /initiatives/{ini}
    public function retrieveInitiative($request, $response, $params)
    {
        $init = $this->resources['initiative']->retrieveInitiative(
            $request->getAttribute('subject'),
            Utils::sanitizedIdParam('ini', $params),
            $request->getQueryParams()
        );
        return $this->sendEntityResponse($response, $init);
    }
}
