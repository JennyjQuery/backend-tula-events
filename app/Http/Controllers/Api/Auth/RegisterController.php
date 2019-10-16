<?php


namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
class RegisterController extends LoginController
{
    /**
     * Register user and assign role client to him
     *
     * @param \Illuminate\Http\Request $request
     * @return User
     */
    public function register(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string',
            'surname' => 'required|string',
            'phone' => 'required|string|unique:users,email',
            'date_of_birth' => 'nullable|date',
            'sex' => 'nullable|tinyInt',
            'avatar' => 'nullable|string',
            'name_organization' => 'nullable|string'
        ];



        $this->validate($request, $rules);
        $user = $this->createUser($request->all());
        $user->assignRole('participant');


        Auth::login($user);

        return $this->sendLoginResponse($request);

    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function createUser(array $data): User
    {
        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'name' => $data['name'],
            'surname' => $data['surname'],
            'phone' => $data['phone'],
            'date_of_birth' => $data['date_of_birth'],
            'sex' => $data['sex'],
            'avatar' => $data['avatar'],
            'name_organization' => $data['name_organization']
        ]);
        return $user;
    }

    /**
     * Register Organizer
     *
     * @param \Illuminate\Http\Request $request
     * @return User
     */
    public function registerOrganizer(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string',
            'surname' => 'required|string',
            'phone' => 'required|string|unique:users,email',
            'avatar' => 'nullable|string',
            'name_organization' => 'required|string'
        ];

        $this->validate($request, $rules);
        $data = $request->all();

        // create user for login
        $user = $this->createUser($data);
        $user->assignRole('organizer');
        Auth::login($user);

        return $this->sendLoginResponse($request);
        //dispatch(new SendVerificationEmail($user));
    }

    /**
     * @param $token
     * @return mixed
     */
    public function verify($token)
    {
        $user = User::where('email_token', $token)->first();
        $user->verified = true;
        if ($user->save()) {
            $url = env("REDIRECT_AFTER_VERIFICATION", "http://chastonui.herokuapp.com/verification");
            return Redirect::to($url);
        }
    }
}
