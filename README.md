# Auto-buy the John McAfee coins he posts on Twitter at Bittrex

Our good friend [@officialmcafee](https://twitter.com/officialmcafee) tries to pump & dump coins on Twitter, with relative good success rates. Coins he mentions go up 100% in a matter of minutes, to come back down to ~60% increase in their value. If you can buy & sell quick enough, you can make a good profit.

The keyword being *quick enough*. That's what this bot aims to do.

It monitors [@officialmcafee](https://twitter.com/officialmcafee)'s tweets, parses their text & images (using OCR), extracts the coin-ticker and buys them on [BitTrex](https://bittrex.com/).

# Disclaimer - you probably don't want to use this!

Judging by the amount of responses I'm getting, similar bots like these exist by the hundreds. Assume a multitude of users per bot. You can't win this game, I suggest you look at the source for educational purposes, but don't trade actual money/btc.

*I am not responsible for any money you lose.*

# Donations

If this bot made you money in any way, I appreciate a donation:

- $BTC: 3H1iYwKhS3QtebfEE5xysouWzm924rdM9i
- $XRP: rDsbeomae4FXwgQTJp9Rs64Qg9vDiTCdBv (tag: 71711751)
- $LTC: MVoVfRajqhrg88kvBRpqw14UkdYh9DahkE
- $ETH: 0x2e128de799121b93a29ff35d15fc35c15236c673
- $BCH: lol no

This bot is offered free of charge with no guarantees whatsoever.

# Prerequisites

This has only been tested on a Mac, might work on Linux if you can get the OCR part going.

# Installation

Clone the repo, then do:

```
$ composer install
```

Add the Bittrex and Twitter API keys in your `.env` file. Your BitTrex API key need full access, except for the withdrawal privilege. Both limit & market buys need to be enabled.

```
$ cat .env
TWITTER_CONSUMER_KEY=
TWITTER_CONSUMER_SECRET=
TWITTER_ACCESS_TOKEN=
TWITTER_ACCESS_TOKEN_SECRET=

BITTREX_KEY=
BITTREX_SECRET=
```

Make sure gocr is installed:

```
$ brew install gocr
```

Now run the bot every 60s (twitter API limit):

```
$ watch -n 60 php artisan mcafee:get-dem-shills -v
```

That's it.

# How to define buy/sell ratio & quantity?

It's hard coded now [in GetMcafeeShills.php](https://github.com/mattiasgeniar/McAfee-Shill-Bot/blob/master/app/Console/Commands/GetMcafeeShills.php). Check lines 90 to 95.

It defines the quantity (right now: 0.01BTC) and how much you're willing to pay for the coin in percentages and when to set the sell target, after 60s.

This would be better fit as a CLI argument or a config, so feel free to contribute.

# Beta

This has been tested on the latest McAfee shill, but if he changes tactics (ie: color-backgrounded images), the OCR might trigger unwanted results.

# I know PHP but don't know Laravel, where's the code?

It's very simple: [app/Console/Commands/GetMcafeeShills.php](https://github.com/mattiasgeniar/McAfee-Shill-Bot/blob/master/app/Console/Commands/GetMcafeeShills.php).
