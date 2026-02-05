<?php

namespace App\Services;

use App\Models\User;
use Bavix\Wallet\Models\Wallet;
interface IWalletService
{
    public function createWallet(User $user, string $name, string $slug): Wallet;
    public function getWallet(User $user, string $slug): ?Wallet;
    public function getUserWallets(User $user): array;
    public function getBalance(User $user, string $slug): float;
    public function credit(User $user, string $slug, float $amount, ?string $transactionCode = null, ?string $description=null): void;
    public function debit(User $user, string $slug, float $amount, string $description): void;
    public function transfer(User $fromUser, string $fromSlug, User $toUser, string $toSlug, float $amount, string $description): void;
}
