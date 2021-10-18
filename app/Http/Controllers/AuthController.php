<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Authenticate user.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try
        {
            // get error
            $error = static::validateRequest(
                \Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required'
                ])
            );

            // count error
            if (count($error) > 0)
            {
                // response
                return $this->respondWithError($error);
            }

            // extract all
            extract($request->all());

            if(! \Auth::attempt(['email'=>$email,'password'=>$password]))
            {
                // response
                return $this->respondWithError(['message' => 'Invalid credentials!']);
            }

            // login user
            $user = User::where('email',$email)->firstOrFail();
            \Auth::login($user);

            // response
            return $this->respondWithSuccess([
                'token' => $user->createToken('access')->plainTextToken,
                'user' => $user
            ]);
        }
        catch(\Exception $e)
        {
            // response
            $this->respondWithError(['message'=> $e->getMessage()]);
        }
    }

    /**
     * Logged out current logged in user.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->respondWithSuccess(['message' => 'OK']);
    }
}
