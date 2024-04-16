<?php

namespace Salle\LSCryptoNews\Utils;

class MarketPrices
{
    public const BITCOIN = ['name' => 'Bitcoin', 'price' => 60000];
    public const ETHEREUM = ['name' => 'Ethereum', 'price' => 2000];
    public const BINANCE_COIN = ['name' => 'Binance Coin', 'price' => 400];
    public const CARDANO = ['name' => 'Cardano', 'price' => 1.5];
    public const SOLANA = ['name' => 'Solana', 'price' => 150];
    public const XRP = ['name' => 'XRP', 'price' => 1];
    public const POLKADOT = ['name' => 'Polkadot', 'price' => 30];
    public const DOGECOIN = ['name' => 'Dogecoin', 'price' => 0.3];
    public const CHAINLINK = ['name' => 'Chainlink', 'price' => 25];
    public const LITECOIN = ['name' => 'Litecoin', 'price' => 150];
    public const STELLAR = ['name' => 'Stellar', 'price' => 0.5];
    public const UNISWAP = ['name' => 'Uniswap', 'price' => 20];
    public const BITCOIN_CASH = ['name' => 'Bitcoin Cash', 'price' => 800];
    public const VECHAIN = ['name' => 'VeChain', 'price' => 0.1];
    public const THETA = ['name' => 'Theta', 'price' => 10];

    // Additional coins
    public const POLYGON = ['name' => 'Polygon', 'price' => 1.2];
    public const COSMOS = ['name' => 'Cosmos', 'price' => 30];
    public const EOS = ['name' => 'EOS', 'price' => 5];
    public const TEZOS = ['name' => 'Tezos', 'price' => 6];
    public const AAVE = ['name' => 'Aave', 'price' => 350];
    public const FILECOIN = ['name' => 'Filecoin', 'price' => 150];
    public const MONERO = ['name' => 'Monero', 'price' => 300];
    public const NEO = ['name' => 'Neo', 'price' => 60];
    public const ALGORAND = ['name' => 'Algorand', 'price' => 1.5];
    public const TRON = ['name' => 'Tron', 'price' => 0.1];
    public const DASH = ['name' => 'Dash', 'price' => 150];
    public const ZCASH = ['name' => 'Zcash', 'price' => 100];
    public const IOTA = ['name' => 'IOTA', 'price' => 1.2];
    public const WAVES = ['name' => 'Waves', 'price' => 30];
    public const NEM = ['name' => 'NEM', 'price' => 0.3];
    public const RAVENCOIN = ['name' => 'Ravencoin', 'price' => 0.05];
    public const HARMONY = ['name' => 'Harmony', 'price' => 0.15];
    public const BASIC_ATTENTION_TOKEN = ['name' => 'Basic Attention Token', 'price' => 0.8];

    public static function getAll(): array
    {
        return [
            self::BITCOIN,
            self::ETHEREUM,
            self::BINANCE_COIN,
            self::CARDANO,
            self::SOLANA,
            self::XRP,
            self::POLKADOT,
            self::DOGECOIN,
            self::CHAINLINK,
            self::LITECOIN,
            self::STELLAR,
            self::UNISWAP,
            self::BITCOIN_CASH,
            self::VECHAIN,
            self::THETA,
            self::POLYGON,
            self::COSMOS,
            self::EOS,
            self::TEZOS,
            self::AAVE,
            self::FILECOIN,
            self::MONERO,
            self::NEO,
            self::ALGORAND,
            self::TRON,
            self::DASH,
            self::ZCASH,
            self::IOTA,
            self::WAVES,
            self::NEM,
            self::RAVENCOIN,
            self::HARMONY,
            self::BASIC_ATTENTION_TOKEN
        ];
    }
}
