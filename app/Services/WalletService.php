<?php

namespace App\Services;


use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\IWalletRepository;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class WalletService implements IWalletService
{

    protected  IWalletRepository $walletRepository;
    public function __construct(IWalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }
    public function createWallet(User $user, string $name, string $slug): Wallet
    {
        if (!$user->hasWallet('savings_wallet')) {
            $user->createWallet([
                'name' => 'savings_wallet',
                'slug' => 'savings_wallet',
            ]);
        }
        return $this->walletRepository->createWallet($user, $name, $slug);
    }

    public function getWallet(User $user, string $slug): ?Wallet
    {
        return $this->walletRepository->findWallet($user, $slug);
    }

    public function getBalance(User $user, string $slug): float
    {
        $wallet = $this->getWallet($user, $slug);
        return $wallet?->balance ?? 0.0;
    }

    /**
     * @throws ExceptionInterface
     */
    public function credit(User $user, string $slug, float $amount, ?string $transactionCode = null, ?string $description="credit wallet"): void
    {
        $wallet = $this->getWallet($user, $slug);

        if (!$wallet) {
            throw new ModelNotFoundException("Wallet with slug '{$slug}' not found for this user.");
        }

        if ($transactionCode && WalletTransaction::where('transaction_code', $transactionCode)->exists()) {
            throw new \Exception("Duplicate transaction detected: {$transactionCode}");
        }

        $wallet->deposit($amount, ['description' => $description]);
        $this->getWallet($user, "savings_wallet")->deposit($amount, ['description' => $description]);


        $this->walletRepository->recordTransaction([
            'wallet_id'        => $wallet->id,
            'type'             => 'credit',
            'amount'           => $amount,
            'description'      => $description,
            'transaction_code' => $transactionCode ?? Str::uuid()->toString(),
        ]);
        $wallet->refresh();
    }

    /**
     * @throws ExceptionInterface
     */
    public function debit(User $user, string $slug, float $amount, string $description="debit wallet"): void
    {
        $wallet = $this->getWallet($user, $slug);

        if (!$wallet) {
            throw new ModelNotFoundException("Wallet with slug '{$slug}' not found for this user.");
        }

        if ($wallet->balance < $amount) {
            throw new \Exception("Insufficient funds in wallet '{$slug}'.");
        }

        $wallet->withdraw($amount, ['description' => $description]);
        $this->getWallet($user, "savings_wallet")->withdraw($amount, ['description' => $description]);

        $this->walletRepository->recordTransaction([
            'wallet_id'        => $wallet->id,
            'type'             => 'debit',
            'amount'           => $amount,
            'description'      => $description,
            'transaction_code' => Str::uuid()->toString(),
        ]);
        $wallet->refresh();
    }

    /**
     * @throws ExceptionInterface
     */
    public function transfer(User $fromUser, string $fromSlug, User $toUser, string $toSlug, float $amount, string $description='wallet transfer'): void
    {
        $senderWallet = $this->getWallet($fromUser, $fromSlug);
        $receiverWallet = $this->getWallet($toUser, $toSlug);

        if (!$senderWallet || !$receiverWallet) {
            throw new ModelNotFoundException("Sender or receiver wallet not found.");
        }

        if ($senderWallet->balance < $amount) {
            throw new \Exception("Insufficient funds in sender's wallet '{$fromSlug}'.");
        }
        $senderWallet->transfer($receiverWallet, $amount,['description' => $description]);
        $this->getWallet($fromUser, "savings_wallet")->withdraw($amount, ['description' => $description]);
        $this->getWallet($toUser, "savings_wallet")->deposit($amount, ['description' => $description]);

        $this->walletRepository->recordTransaction([
            'wallet_id'        => $senderWallet->id,
            'type'             => 'transfer',
            'amount'           => $amount,
            'description'      => $description,
            'transaction_code' => Str::uuid()->toString(),
        ]);
        $senderWallet->refresh();
        $receiverWallet->refresh();
    }
    public function getUserWallets(User $user): array
    {
        return $this->walletRepository->getUserWallets($user);
    }
}
