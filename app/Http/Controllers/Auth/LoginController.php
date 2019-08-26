<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Services\MarketAuthenticationService;
use App\Services\MarketService;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    protected $marketAuthenticationService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MarketAuthenticationService $marketAuthenticationService, MarketService $marketService)
    {
        $this->middleware('guest')->except('logout');
        $this->marketAuthenticationService = $marketAuthenticationService;

        parent::__construct($marketService);
    }

        /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $authorizationUrl = $this->marketAuthenticationService->resolveAutrizationUrl();
        return view('auth.login')->with(['authorizationUrl' => $authorizationUrl]);
    }

    public function authorization(Request $request)
    {
        if ($request->has('code')) {
            $tokenData = $this->marketAuthenticationService->getCodeToken($request->code);
            $userData = $this->marketService->getUserInformation();
            $user = $this->registerOrUpdateUser($userData,$tokenData);
            $this->loginUser($user);
            return redirect()->intended('home');
        }
        return redirect()->route('login')->withErrors(['Se cancelo la autenticacion']);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
        $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            
            return $this->sendLockoutResponse($request);
        }
        
        try {
            $tokenData = $this->marketAuthenticationService->getPasswordToken($request->email, $request->password);
            $userData = $this->marketService->getUserInformation();
            $user = $this->registerOrUpdateUser($userData,$tokenData);
            $this->loginUser($user, $request->has('remenber'));
            return redirect()->intended('home');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $message = $e->getResponse()->getBody();
            if (Str::contains($message, 'invalid_credentials')) {
                // If the login attempt was unsuccessful we will increment the number of attempts
                // to login and redirect the user back to the login form. Of course, when this
                // user surpasses their maximum number of attempts they will get locked out.
                $this->incrementLoginAttempts($request);
                return $this->sendFailedLoginResponse($request);
            }
            throw $e;
        }
    }

    public function registerOrUpdateUser($userData, $tokenData)
    {
        return User::updateOrCreate(
            [
                'service_id' => $userData->identifier
            ],
            [
                'grant_type' => $tokenData->grant_type,
                'access_token' => $tokenData->access_token,
                'refresh_token' => $tokenData->refresh_token,
                'token_expired_at' => $tokenData->token_expired_at,
            ]);
    }

    public function loginUser(User $user, $remember = true)
    {
        Auth::login($user, $remember);
        session()->regenerate();
    }
}
