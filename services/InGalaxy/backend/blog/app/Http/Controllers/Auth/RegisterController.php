<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/news';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'login' => ['required', 'string', 'max:255','unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'name' => ['string', 'max:255','nullable'],
            'surname' => ['string', 'max:255','nullable'],
            'race' => ['string', 'max:255','nullable'],
            'gender' => ['string', 'max:255','nullable'],
            'birthday' => ['date','nullable'],
            'homeplace' => ['string', 'max:255','nullable'],
            'file' => ['image','nullable'],
            'selfdescription' => ["string",'nullable']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data, Request $request)
    {
        $file = $request->file('file');


        if(!empty($file)) {
            //Move Uploaded File
            $destinationPath = 'img/uploads';

            $newFileName = md5($file->getClientOriginalName());
            $file->move($destinationPath, $newFileName);

            $data['avatar'] = $destinationPath . "/" . $newFileName;
        }else $data['avatar']="img/galaxy.jpg";

        return User::create([
            'login' => $data['login'],
            'password' => Hash::make($data['password']),
            'name' => $data['name'],
            'surname' => $data['surname'],
            'race' => $data['race'],
            'gender' => $data['gender'],
            'birthday' => $data['birthday'],
            'homeplace' => $data['homeplace'],
            'avatar'=>$data['avatar'],
            'selfdescription'=>$data['selfdescription']
        ]);
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all(),$request)));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
}
