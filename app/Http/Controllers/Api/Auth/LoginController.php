<?php


namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse | array
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $user = $this->guard()->user();

        $personalAccessToken = $user->createToken(config('auth.auth_client_name'));

        $user->withAccessToken($personalAccessToken->token);

        return array_merge($user->toArray(), ['api_token' => $personalAccessToken->accessToken]);

    }

    /**
     * Delete token user has authorized with
     */


    /*    public function logout()
        {
            // delete token and related device (by database constraint ON CASCADE)
            Auth::user()->token()->delete();
        }*/
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        //Auth::user()->token()->delete();
        //Auth::logout();
    }
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
  /*  protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'|'string' ,
            'password' => 'required|string',
        ]);
    }*/

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    /*    protected function validateLogin(Request $request)
        {
            $this->validate($request, [
                $this->username() => ['required', 'string', new EmailOrPhone()],
                'password' => 'required|string',
            ]);
        }*/

    /**
     * Get the needed authorization credentials from the request.
     * If 'login' looks like an email use email as a username()
     * in other cases use 'phone'
     *
     * Simply say just rename 'login' key to 'email' or 'phone' based on 'login' value
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
/*    protected function credentials(Request $request)
    {
        $credentials = [];

        $login = $request->input($this->username());

        if ($this->is_email($login)) {
            $credentials['email'] = $login;
        } else {
            $credentials['phone'] = PhoneNumber::make($login, 'RU')->formatE164();
        }

        $credentials['password'] = $request->input('password');

        return $credentials;
    }*/

    /**
     * Test if value a valid email
     *
     * @param string $value
     * @return bool
     */
    protected function is_email($value)
    {
        return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}
