<?php

namespace Salle\LSCryptoNews\Utils;

class NewsFiles
{
    public static function getAllArticles(): array
    {
        return [
            [
                'title' => 'Bitcoin Hits New All-Time High',
                'date' => '2024-04-15',
                'author' => 'CryptoNews',
                'summary' => 'Bitcoin reached a new all-time high of $100,000, fueled by increasing institutional adoption.',
            ],
            [
                'title' => 'Ethereum Surpasses $5,000 Mark',
                'date' => '2024-04-16',
                'author' => 'Crypto Gazette',
                'summary' => 'Ethereum breaks through the $5,000 barrier, setting a new record for the cryptocurrency.',
            ],
            [
                'title' => 'Dogecoin Soars Amidst Market Turmoil',
                'date' => '2024-04-17',
                'author' => 'Dogecoin Digest',
                'summary' => 'Dogecoin defies market trends, skyrocketing by 200% in a single day.',
            ],
            [
                'title' => 'Ripple Launches New Partnership Program',
                'date' => '2024-04-18',
                'author' => 'Ripple Insider',
                'summary' => 'Ripple announces a new partnership initiative aimed at expanding its network of collaborators.',
            ],
            // Add more articles here
            [
                'title' => 'Litecoin Foundation Announces Major Upgrade',
                'date' => '2024-04-19',
                'author' => 'Litecoin Times',
                'summary' => 'The Litecoin Foundation unveils a major upgrade to the Litecoin blockchain, introducing new features and improvements.',
            ],
            [
                'title' => "Cardano's Founder Hosts AMA Session",
                'date' => '2024-04-20',
                'author' => 'Cardano Chat',
                'summary' => "Charles Hoskinson, the founder of Cardano, hosts an Ask Me Anything (AMA) session to answer questions from the community.",
            ],
            [
                'title' => 'VeChain Partners with Global Retail Giant',
                'date' => '2024-04-21',
                'author' => 'VeChain Weekly',
                'summary' => 'VeChain announces a strategic partnership with a leading global retail chain to implement blockchain technology in supply chain management.',
            ],
            [
                'title' => 'Polkadot Launches Parachain Auctions',
                'date' => '2024-04-22',
                'author' => 'Polkadot Post',
                'summary' => 'Polkadot initiates its parachain auctions, allowing projects to compete for slots on its network and expand its ecosystem.',
            ],
            [
                'title' => 'Chainlink Integrates with Top DeFi Platforms',
                'date' => '2024-04-23',
                'author' => 'Chainlink Chronicle',
                'summary' => 'Chainlink announces integrations with several leading decentralized finance (DeFi) platforms, enhancing data reliability and security.',
            ],
            [
                'title' => 'Stellar Foundation Grants Fund Innovation Projects',
                'date' => '2024-04-24',
                'author' => 'Stellar Insights',
                'summary' => 'The Stellar Development Foundation allocates grants to support innovative projects building on the Stellar blockchain, fostering growth and adoption.',
            ],
        ];
    }
}
