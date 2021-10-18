<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        try
        {
            // get error
            $error = static::validateRequest(
                \Validator::make($request->all(), [
                    'name' => 'required',
                    'user_name' => 'required',
                    'email' => 'required|email',
                    'user_role' => 'required',
                    'avatar' => 'required|dimensions:width=256,height=256'
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

            // start transaction
            \DB::beginTransaction();
                
                // user
                $user = User::findOrFail($id);
                $user->name = $name ?? NULL;
                $user->user_name = $user_name ?? NULL;
                $user->email = $email ?? NULL;
                $user->user_role = $user_role ?? NULL;

                // check avatar
                if($avatar)
                {
                    $user->avatar = \Storage::disk("public")->putFile(User::AVATAR_PATH, $avatar);
                }

                // update
                $user->save();

            // commit transaction
            \DB::commit();

            // response
            return $this->respondWithSuccess(['message'=>'Your profile is successfully updated!']);
        }
        catch(\Exception $e)
        {
            // response
            $this->respondWithError(['message' => $e->getMessage()]);
        }
    }
}
