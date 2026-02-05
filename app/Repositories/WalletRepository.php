<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\WalletTransaction;
use Bavix\Wallet\Internal\Repository\WalletRepositoryInterface;
use Bavix\Wallet\Models\Wallet;

class WalletRepository implements IWalletRepository
{

    public function createWallet(User $user, string $name, string $slug): Wallet
    {
        return $user->wallets()->firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );
    }

    public function findWallet(User $user, string $slug): ?Wallet
    {
        return $user->wallets()->where('slug', $slug)->first();
    }

    public function findWalletById(int $walletId): ?Wallet
    {
        return Wallet::find($walletId);
    }

    public function getUserWallets(User $user): array
    {
        $wallets = $user->wallets;

        if ($wallets->isEmpty()) {
            return [];
        }

        return $wallets->map(function ($wallet) {
            return [
                'wallet_name' => $wallet->name,
                'wallet_type' => $wallet->slug,
                'balance'     => (float) $wallet->balance,
            ];
        })->toArray();
    }

    public function recordTransaction(array $data)
    {
        return WalletTransaction::create($data);
    }
}
