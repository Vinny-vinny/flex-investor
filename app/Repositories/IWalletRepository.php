<?php

namespace App\Repositories;

use App\Models\User;
use Bavix\Wallet\Models\Wallet;

interface IWalletRepository
{
    public function createWallet(User $user, string $name, string $slug): Wallet;

    public function findWallet(User $user, string $slug): ?Wallet;

    public function findWalletById(int $walletId): ?Wallet;

    public function getUserWallets(User $user): array;

    public function recordTransaction(array $data);
}
