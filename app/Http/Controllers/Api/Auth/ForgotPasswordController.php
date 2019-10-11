<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    use SendsPasswordResetEmails;

    public function construct()
    {
        $this->middleware('guest');
    }
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function getResetToken(Request $request)
    {
        $is_email = (bool) filter_var($request->get('login'), FILTER_VALIDATE_EMAIL);

        if ($is_email) {
            $request->validate(['login' => 'required|email']);

            $user = User::where('email', $request->get('login'))->first();
            if (!$user) {
                throw new ValidationException('User not exists');
            }

            $token = $this->broker()->createToken($user);

            $user->sendPasswordResetNotification($token);

        }

        // if is phone, should be done with sms
    }
}
