<?php

namespace App\Controllers;

class Profile extends BaseController
{
    public function index()
    {
        if ($this->ionAuth->loggedIn() || $this->session->has("LoggedUserData")) {

            // get seesion data
            $sessionData = $this->session->get('LoggedUserData');

            // get row data from Ion Auth
            $users = $this->db->table('users')->getWhere(['email' => $sessionData['email']])->getRowArray();

            // var_dump($users); exit();

            $data = [
                'isLogin' => $this->ionAuth->loggedIn(),
                'isAdmin' => $this->ionAuth->isAdmin(),
                'getUserId' => $this->ionAuth->getUserId(),
                'message' => $this->session->getFlashdata('message'),
                'isLoggedIn' =>  $sessionData, 

                'username' => $users['username'],
                'phone' => $users['phone'],
                'profile_img' => $users['profile_img'],
                'last_login' => $users['last_login'],

                'userId' => [
                    'class' => 'form-control form-control-sm',
                    'name'  => 'userId',
                    'id'    => 'userId',
                    'type'  => 'text',
                    'placeholder' => 'User ID',
                    'value' => $users['id'],
                    'readonly' => 'readonly'
                ],
                'googleId' => [
                    'class' => 'form-control form-control-sm',
                    'name'  => 'googleId',
                    'id'    => 'googleId',
                    'type'  => 'text',
                    'placeholder' => 'Google ID',
                    'value' => $users['google_id'],
                    'readonly' => 'readonly'
                ],
                'firstName' => [
                    'class' => 'form-control form-control-sm',
                    'name'  => 'firstName',
                    'id'    => 'firstName',
                    'type'  => 'text',
                    'placeholder' => 'First Name',
                    'value' => $users['first_name'],
                    'readonly' => 'readonly'
                ],
                'lastName' => [
                    'class' => 'form-control form-control-sm',
                    'name'  => 'lastName',
                    'id'    => 'lastName',
                    'type'  => 'text',
                    'placeholder' => 'Last Name',
                    'value' => $users['last_name'],
                    'readonly' => 'readonly'
                ],
                'email' => [
                    'class' => 'form-control form-control-sm',
                    'name'  => 'email',
                    'id'    => 'email',
                    'type'  => 'text',
                    'placeholder' => 'Email',
                    'value' => $users['email'],
                    'readonly' => 'readonly'
                ],
                'phone' => [
                    'class' => 'form-control form-control-sm',
                    'name'  => 'phone',
                    'id'    => 'phone',
                    'type'  => 'text',
                    'placeholder' => 'Phone',
                    'value' => $users['phone'],
                    'readonly' => 'readonly'
                ],
            ];

            return view('layout/profile', $data);
        } else {
            // redirect them to the login page
            return redirect()->to(base_url());
        }
    }
}
