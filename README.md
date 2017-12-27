# Auto-buy the John McAfee coins he posts on Twitter at Bittrex

Our good friend [@officialmcafee](https://twitter.com/officialmcafee) tries to pump & dump coins on Twitter, with relative good success rates. Coins he mentions go up 100% in a matter of minutes, to come back down to ~60% increase in their value. If you can buy & sell quick enough, you can make a good profit.

The keyword being *quick enough*. That's what this bot aims to do.

It monitors [@officialmcafee](https://twitter.com/officialmcafee)'s tweets, parses their text & images (using OCR), extracts the coin-ticker and buys them on [BitTrex](https://bittrex.com/).

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

Add the Bittrex and Twitter API keys in your `.env` file.

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
watch -n 60 php artisan mcafee:get-dem-shills -v
```

That's it.

# Beta

This has been tested on the latest McAfee shill, but if he changes tactics (ie: color-backgrounded images), the OCR might trigger unwanted results.

# I know PHP but don't know Laravel, where's the code?

It's very simple: [app/Console/Commands/GetMcafeeShills.php](blob/master/app/Console/Commands/GetMcafeeShills.php).
