<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

use App\Mail\InvitationLink;
use App\Mail\CreateAccount;

use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Send invitation link in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function invite(Request $request): JsonResponse
    {
        try
        {
            // get error
            $error = static::validateRequest(
                \Validator::make($request->all(), [
                    'email' => 'required|email'
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

                // create user
                $user = User::firstOrCreate(['email' => $email]);

                // encrypt email
                $email = Crypt::encryptString($email);

                // send invitation link email
                //\Mail::to($email)->send(new InvitationLink($email));

            // commit transaction
            \DB::commit();

            // response
            return $this->respondWithSuccess([
                'email' => $email,
                'message' => 'Invitation link is successfully sent!'
            ]);
        }
        catch(\Exception $e)
        {
            // response
            $this->respondWithError(['message' => $e->getMessage()]);
        }
    }

    /**
     * Create a newly registered account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $email
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request, $email): JsonResponse
    {
        try
        {
            // get error
            $error = static::validateRequest(
                \Validator::make($request->all(), [
                    'user_name' => 'required|min:4|max:20',
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

            // start transaction
            \DB::beginTransaction();

                // decrypt email
                $email = Crypt::decryptString($email);

                // get 6 digit pin
                $pin = mt_rand(100000,999999);

                // update user
                $user = User::where('email',$email)->firstOrFail();
                $user->user_name = $user_name;
                $user->password = \Hash::make($password);
                $user->pin = $pin; // random 6 digit pin
                $user->save();

                // send 6 digit pin email
                //\Mail::to($email)->send(new CreateAccount($pin));

            // commit transaction
            \DB::commit();

            // response
            return $this->respondWithSuccess([
                'pin' => $pin,
                'message' => 'Your 6 digit pin is successfully sent!'
            ]);
        }
        catch(\Exception $e)
        {
            // response
            $this->respondWithError(['message' => $e->getMessage()]);
        }
    }

    /**
     * Verify account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String  $email
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $email): JsonResponse
    {
        try
        {
            // get error
            $error = static::validateRequest(
                \Validator::make($request->all(), [
                    'pin' => 'required|int'
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
                
                // decrypt email
                $email = Crypt::decryptString($email);

                // get user
                $user = User::where('email',$email)->firstOrFail();

                // validate pin
                if($user->pin!=$pin)
                {
                    // response
                    return $this->respondWithError(['nessage' => 'Incorrect pin please try again!']);
                }

                // update registered at
                $user->registered_at = Carbon::now();
                $user->email_verified_at = Carbon::now();
                $user->save();

            // commit transaction
            \DB::commit();

            // response
            return $this->respondWithSuccess([
                'token' => $user->createToken('access')->plainTextToken,
                'user' => $user
            ]);
        }
        catch(\Exception $e)
        {
            // response
            $this->respondWithError(['message' => $e->getMessage()]);
        }
    }
}
