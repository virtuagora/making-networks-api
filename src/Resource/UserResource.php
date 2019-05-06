<?php

namespace App\Resource;

use App\Mail\SignUpEmail;
use App\Util\Exception\AppException;
use App\Util\Utils;

class UserResource extends Resource
{
    public function retrieveSchema($options = [])
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'names' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 25,
                ],
                'surnames' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 25,
                ],
                'password' => [
                    'type' => 'string',
                    'minLength' => 4,
                    'maxLength' => 250,
                ],
                // 'email' => [
                //     'type' => 'string',
                //     'format' => 'email',
                // ],
                // 'token' => [
                //     'type' => 'string',
                //     'minLength' => 10,
                //     'maxLength' => 100,
                // ],
            ],
            'required' => ['names', 'surnames', 'password'],
            'additionalProperties' => false,
        ];
        return $schema;
    }

    public function createUser($subject, $data, $token)
    {
        $v = $this->validation->fromSchema($this->retrieveSchema());
        $v->assert($data);
        $user = $this->identity->signUp('local', $token, $data);
        return $user;
    }

    public function retrieveUser($subject, $id, $options = [])
    {
        return $this->db->query('App:Subject')
            ->where('type', 'User')
            ->findOrFail($id);
    }

    public function createPendingUser($subject, $data)
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'format' => 'email',
                ],
            ],
            'required' => ['email'],
            'additionalProperties' => false,
        ];
        $v = $this->validation->fromSchema($schema);
        $v->assert($this->validation->prepareData($schema, $data, true));
        $dupFields = $this->db->findDuplicatedFields('App:Person', [
            'email' => $data['email']
        ]);
        if (count($dupFields) > 0) {
            throw new AppException(
                'Email already registered', 'registeredEmail'
            );
        }
        $pending = $this->db->query('App:Token')->firstOrNew([
            'type' => 'signUp',
            'finder' => $data['email'],
        ]);
        $pending->token = Utils::randomStr(50);
        $pending->data = [
            'email' => $data['email'],
        ];
        $pending->save();
        $mailArg = [
            'acceso' => $pending->finder,
            'token' => $pending->token,
        ];
        $mail = new SignUpEmail($mailArg);
        $this->mailer->to($pending->finder)->send($mail);
        return $pending;
    }
}