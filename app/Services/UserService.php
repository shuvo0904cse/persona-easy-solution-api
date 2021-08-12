<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Get Logged In User Id
     */
    public function getLoggedInUserId()
    {
        $user = $this->getLoggedInUserDetails();
        return !empty($user) ? $user->id : null;
    }

    /**
     * Get Logged In User Details
     */
    public function getLoggedInUserDetails()
    {
        return Auth::check() ? Auth::user() : null;
    }
}
