<?php


namespace App\Http\Controllers\Api\Auth;

use App\Models\Account;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Rules\EmailOrPhone;
use Illuminate\Auth\Events\Registered;
use App\Jobs\SendVerificationEmail;

class RegisterController
{
    /**
     * Register user and assign role client to him
     *
     * @param \Illuminate\Http\Request $request
     * @return User
     */
    public function register(Request $request)
    {
        $max = config('app.max_string_length');

        $rules = [
            'name' => 'required|string|max:' . $max . '',
            'login' => ['required', 'string', new EmailOrPhone()],
            'password' => 'required|string|min:6|max:' . $max . '|confirmed',
            'agreement' => 'accepted',
        ];

        $this->validate($request, $rules);
        $user = $this->createUser($request->all());
        if ($this->is_email($request->get('login'))) {
            event(new Registered($user));
            $user->assignRole('client');
            dispatch(new SendVerificationEmail($user));
            return $user;
        }

        Auth::login($user);

        return $this->sendLoginResponse($request);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function createUser(array $data): User
    {
        $email = $phone = null;
        $loginFieldName = 'phone';

        if ($this->is_email($data['login'])) {
            $email = $data['login'];
            $loginFieldName = 'email';
        } else {
            $phone = PhoneNumber::make($data['login'], 'RU' )->formatE164();
        }

        $this->getValidationFactory()->make(
            $data,
            ['login' => 'unique:users,' . $loginFieldName]
        )->validate();
        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'phone' => $phone,
            'password' => bcrypt($data['password']),
            'email_token' => base64_encode($email),
            'verified' => $phone ? true : false
        ]);
        Account::create([
            'user_id' => $user->id,
            'balance' => config('chaston.initial_user_balance'),
        ]);
        return $user;
    }

    /**
     * Register provider
     *
     * @param \Illuminate\Http\Request $request
     * @return User
     */
    public function registerProvider(Request $request)
    {
        $max = config('app.max_string_length');
        $providerTypes = (new Provider)->getTypes();

        $rules = [
            'type' => ['required', Rule::in($providerTypes)],
            'name' => 'required|string|max:' . $max . '',
            'last_name' => 'required|string|max:' . $max . '',
            'email' => 'required|unique:users,email',
            'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
            'category_id' => 'required|exists:categories,id',
            'password' => 'required|string|min:6|max:' . $max . '|confirmed',
            'agreement' => 'accepted',
        ];

        $this->validate($request, $rules);

        $request->merge(['login' => $request->email]);
        $data = $request->all();

        // create user for login
        event(new Registered($user = $this->createUser($data)));
        $user->assignRole('provider');
        dispatch(new SendVerificationEmail($user));

        // then create provider
        $provider = Provider::create([
            'name' => $data['name'],
            'type' => array_flip($providerTypes)[$data['type']],
            'email' => $data['email'],
            'phones' => [['phone' => $data['phone']]],
            'category_id' => $data['category_id'],
            'user_id' => $user->id,
        ]);
        return $provider;
    }

    /**
     * @param $token
     * @return mixed
     */
    public function verify($token)
    {
        $user = User::where('email_token',$token)->first();
        $user->verified = true;
        if($user->save()) {
            $url = env("REDIRECT_AFTER_VERIFICATION", "http://chastonui.herokuapp.com/verification");
            return Redirect::to($url);
        }
    }
}
