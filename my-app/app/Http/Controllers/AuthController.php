<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ParticipantVerificationEmail;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // For now, registration creates an LGU-admin account.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // cast() in User model hashes this
            'role' => 'LGU_ADMIN',
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function showParticipantLogin()
    {
        return view('auth.participant-login');
    }

    public function participantLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if user is a participant
            if ($user->role !== 'PARTICIPANT') {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'This login is for participants only. Please use the admin/trainer login.'])
                    ->onlyInput('email');
            }

            // Check if participant is active
            if ($user->status === 'inactive') {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Your account has been deactivated. Please contact an administrator.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function showParticipantRegister()
    {
        return view('auth.participant-register');
    }

    /**
     * Step 1: Start registration - validate and store data in session, generate verification code
     */
    public function participantRegisterStart(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
                'required_without:phone',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                'unique:users,phone',
                'required_without:email',
            ],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'email.required_without' => 'Please provide either an email or phone number.',
            'phone.required_without' => 'Please provide either an email or phone number.',
            'email.unique' => 'This email is already registered. Please log in or use a different one.',
            'phone.unique' => 'This phone number is already registered. Please log in or use a different one.',
        ]);

        // Check if email or phone already exists with friendly message
        if (isset($data['email']) && User::where('email', $data['email'])->exists()) {
            return back()
                ->withErrors(['email' => 'This email is already registered. Please log in or use a different one.'])
                ->withInput();
        }

        if (isset($data['phone']) && User::where('phone', $data['phone'])->exists()) {
            return back()
                ->withErrors(['phone' => 'This phone number is already registered. Please log in or use a different one.'])
                ->withInput();
        }

        // Determine verification method
        $verificationMethod = isset($data['email']) ? 'email' : 'phone';
        $verificationCode = null;

        // Generate 6-digit verification code for both email and phone
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        if ($verificationMethod === 'phone') {
            // Send OTP via SMS
            try {
                $smsService = new SmsService();
                $smsService->sendOtp($data['phone'], $verificationCode);
            } catch (\Exception $e) {
                \Log::error("Failed to send SMS OTP: " . $e->getMessage());
                // Still continue - OTP is logged as fallback
                \Log::info("OTP for {$data['phone']}: {$verificationCode}");
            }
        } else {
            // Send verification code via email
            try {
                Mail::to($data['email'])->send(
                    new ParticipantVerificationEmail($verificationCode, $data['name'])
                );
            } catch (\Exception $e) {
                \Log::error("Failed to send verification email: " . $e->getMessage());
                // Still log for development fallback
                \Log::info("Email verification code for {$data['email']}: {$verificationCode}");
            }
        }

        // Store registration data in session
        $request->session()->put('participant_registration', [
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'verification_method' => $verificationMethod,
            'verification_code' => $verificationCode,
            'expires_at' => now()->addMinutes(15), // 15 minutes expiry
        ]);

        return redirect()->route('participant.register.verify')
            ->with('verification_method', $verificationMethod)
            ->with('contact', $verificationMethod === 'email' ? $data['email'] : $data['phone']);
    }

    /**
     * Show verification page
     */
    public function showParticipantRegisterVerify()
    {
        $registrationData = session('participant_registration');
        
        if (!$registrationData) {
            return redirect()->route('participant.register')
                ->withErrors(['form' => 'Registration session expired. Please start again.']);
        }

        $verificationMethod = $registrationData['verification_method'] ?? 'email';
        $contact = $verificationMethod === 'email' 
            ? $registrationData['email'] 
            : $registrationData['phone'];

        return view('auth.participant-register-verify', [
            'verification_method' => $verificationMethod,
            'contact' => $contact,
        ]);
    }

    /**
     * Step 2: Verify and complete registration
     */
    public function participantRegisterVerify(Request $request)
    {
        $registrationData = $request->session()->get('participant_registration');

        if (!$registrationData) {
            return redirect()->route('participant.register')
                ->withErrors(['form' => 'Registration session expired. Please start again.']);
        }

        // Check expiry
        if (now()->isAfter($registrationData['expires_at'])) {
            $request->session()->forget('participant_registration');
            return redirect()->route('participant.register')
                ->withErrors(['form' => 'Verification code expired. Please start registration again.']);
        }

        $verificationMethod = $registrationData['verification_method'];

        // Validate verification code (same for both email and phone)
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Please enter the verification code.',
            'otp.size' => 'Verification code must be 6 digits.',
        ]);

        if ($request->otp !== $registrationData['verification_code']) {
            $contact = $verificationMethod === 'email' 
                ? $registrationData['email'] 
                : $registrationData['phone'];
            
            return back()
                ->withErrors(['otp' => 'Invalid verification code. Please try again.'])
                ->with('verification_method', $verificationMethod)
                ->with('contact', $contact);
        }

        // All verified - create the user
        $participantId = $this->generateParticipantId();

        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'phone' => $registrationData['phone'],
            'password' => $registrationData['password'],
            'role' => 'PARTICIPANT',
            'participant_id' => $participantId,
            'status' => 'active',
            'registered_at' => now(),
        ]);

        // Clear session
        $request->session()->forget('participant_registration');

        Auth::login($user);

        return redirect('/dashboard')
            ->with('status', 'Registration successful! Welcome to the training platform.');
    }

    /**
     * Handle email verification via link (GET request)
     */
    public function participantRegisterVerifyEmail(Request $request, $token)
    {
        $registrationData = $request->session()->get('participant_registration');

        if (!$registrationData) {
            return redirect()->route('participant.register')
                ->withErrors(['form' => 'Registration session expired. Please start again.']);
        }

        // Check expiry
        if (now()->isAfter($registrationData['expires_at'])) {
            $request->session()->forget('participant_registration');
            return redirect()->route('participant.register')
                ->withErrors(['form' => 'Verification link expired. Please start registration again.']);
        }

        if ($registrationData['verification_method'] !== 'email') {
            return redirect()->route('participant.register.verify')
                ->withErrors(['form' => 'Invalid verification method.']);
        }

        if ($token !== $registrationData['verification_token']) {
            return redirect()->route('participant.register.verify')
                ->withErrors(['token' => 'Invalid verification link.'])
                ->with('verification_method', 'email')
                ->with('contact', $registrationData['email']);
        }

        // Token is valid - complete registration
        $participantId = $this->generateParticipantId();

        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'phone' => $registrationData['phone'],
            'password' => $registrationData['password'],
            'role' => 'PARTICIPANT',
            'participant_id' => $participantId,
            'status' => 'active',
            'registered_at' => now(),
        ]);

        // Clear session
        $request->session()->forget('participant_registration');

        Auth::login($user);

        return redirect('/dashboard')
            ->with('status', 'Registration successful! Welcome to the training platform.');
    }

    private function generateParticipantId()
    {
        do {
            $id = 'PART-' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (User::where('participant_id', $id)->exists());

        return $id;
    }

    public function logout(Request $request)
    {
        // Capture role before logging out
        $user = Auth::user();
        $role = $user->role ?? null;

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on last role
        if ($role === 'PARTICIPANT') {
            return redirect()->route('participant.login');
        }

        // Default: admin / trainer login
        return redirect()->route('login');
    }
}




