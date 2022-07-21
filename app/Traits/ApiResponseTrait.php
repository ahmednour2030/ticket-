<?php

namespace App\Traits;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

trait ApiResponseTrait{

    /**
     * [
     *  data =>
     *  status =>
     *  code => 200
     * ]
     * @param null $message
     * @param null $data
     * @param null $errors
     * @param bool $key
     * @param int $status
     * @param null $token
     * @return Application|ResponseFactory|Response
     */
    public function apiResponse(
        $message = null,
        $data = null,
        $errors = null,
        bool $key = true,
        int $status = 200,
        $token = null
    )
    {
        $array = [
            'key' => $key,
            'message' => $message,
        ];

        if(is_null($data) && !is_null($errors)){
            $array['errors'] = $errors;
        }elseif(is_null($errors) && !is_null($data)){
            $array['data'] = $data;
        }

        if($token) $array['token'] = $token;

        return response($array, $status);
    }

    /**
     * This function apiResponseValidation for Validation Request
     * @param $validator
     */
    public function apiResponseValidation($validator)
    {
        $errors = $validator->errors();
        $response = $this->apiResponse('Invalid data send', null, $errors->first(), 422);
        throw new HttpResponseException($response);
    }
}
