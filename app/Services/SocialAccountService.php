<?php

namespace App\Services;

use App\Notifications\WelcomeEmail;
use App\SocialAccount;
use App\User;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAccountService
{
    public function createOrGetUser(ProviderUser $providerUser, $socialProvider)
    {
        $account = SocialAccount::whereProvider($socialProvider)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {

            $account = new SocialAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => $socialProvider,
            ]);

            $user = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail()
            ]);

            $account->user()->associate($user);
            $account->save();

            $user->notify(new WelcomeEmail());

            return $user;

        }

    }
}
