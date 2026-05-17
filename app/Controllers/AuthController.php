<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\CSRF;
use App\Core\RateLimiter;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        // Call parent constructor to ensure global rate limit is applied
        parent::__construct();
    }

    public function login(): void
    {
        // If already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT);
            exit;
        }

        $data = [
            'title' => 'Login',
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Enforce Specific Login Rate Limiting (5 attempts per 15 minutes)
            if (RateLimiter::check('login_attempt', 5, 900)) {
                $data['email_err'] = 'Too many login attempts. Please wait 15 minutes and try again.';
                $this->view('auth/login', $data);
                return;
            }

            CSRF::enforce();
            
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);

            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            if (empty($data['email_err']) && empty($data['password_err'])) {
                $user = User::login($data['email'], $data['password']);

                if ($user) {
                    // Create session
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_name'] = $user->name;
                    $_SESSION['user_role'] = $user->role; // Store role in session
                    
                    // Redirect admin to dashboard, student to home
                    if ($user->isAdmin()) {
                        header('Location: ' . URLROOT . '/admin/dashboard');
                    } else {
                        header('Location: ' . URLROOT);
                    }
                    exit;
                } else {
                    // This is a failed attempt, so we record it.
                    RateLimiter::attempt('login_attempt', 5, 900);
                    $data['password_err'] = 'Password incorrect or email not found';
                }
            }
        }

        $this->view('auth/login', $data);
    }

    public function register(): void
    {
        // If already logged in, redirect to home
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT);
            exit;
        }

        $data = [
            'title' => 'Register',
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRF::enforce();

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data['name'] = trim($_POST['name']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['confirm_password'] = trim($_POST['confirm_password']);

            // Validate Name
            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter name';
            }

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else {
                if (User::findByEmail($data['email'])) {
                    $data['email_err'] = 'Email is already taken';
                }
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be at least 6 characters';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Passwords do not match';
                }
            }

            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                if (User::register($data['name'], $data['email'], $data['password'])) {
                    header('Location: ' . URLROOT . '/auth/login');
                    exit;
                } else {
                    die('Something went wrong registering the user');
                }
            }
        }

        $this->view('auth/register', $data);
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('Location: ' . URLROOT);
        exit;
    }
}