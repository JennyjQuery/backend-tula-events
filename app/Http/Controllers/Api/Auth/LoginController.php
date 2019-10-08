<?php


namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\ProviderAuthorized;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Rules\EmailOrPhone;


class LoginController
{
    use AuthenticatesUsers;

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse | array
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $user = $this->guard()->user();

        $personalAccessToken = $user->createToken(config('auth.oauth_client_name'));

        $user->withAccessToken($personalAccessToken->token);

        $model = $user;
        if ($user->hasRole('provider')) {
            $model = ProviderAuthorized::first();
        }

        return array_merge($model->toArray(), ['api_token' => $personalAccessToken->accessToken]);
    }

    /**
     * Delete token user has authorized with
     */
    public function logout()
    {
        // delete token and related device (by database constraint ON CASCADE)
        Auth::user()->token()->delete();
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'login';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => ['required', 'string', new EmailOrPhone()],
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     * If 'login' looks like an email use email as a username()
     * in other cases use 'phone'
     *
     * Simply say just rename 'login' key to 'email' or 'phone' based on 'login' value
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = [];

        $login = $request->input($this->username());

        if ($this->is_email($login)) {
            $credentials['email'] = $login;
        } else {
            $credentials['phone'] = PhoneNumber::make($login, 'RU' )->formatE164();
        }

        $credentials['password'] = $request->input('password');

        return $credentials;
    }

    /**
     * Test if value a valid email
     *
     * @param string $value
     * @return bool
     */
    protected function is_email($value) {
        return (bool) filter_var($value, FILTER_VALIDATE_EMAIL);
    }

}
