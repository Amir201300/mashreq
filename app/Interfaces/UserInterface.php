<?php

namespace App\Interfaces;

interface UserInterface {
    /**
     * @param $request
     * @param $user
     * @param $type
     * @return mixed
     */
    public function save_user($request,$user,$type);

    /**
     * @param $request
     * @param $user_id
     * @return mixed
     */
    public function validate_user($request,$user_id);

    /**
     * @param $request
     * @return mixed
     */
    public function login($request);
}
