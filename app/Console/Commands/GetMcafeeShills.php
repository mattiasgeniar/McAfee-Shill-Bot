<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Storage;
use Pepijnolivier\Bittrex\Bittrex;

class GetMcafeeShills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcafee:get-dem-shills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse Mcafees timeline and find shills.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $timestampLimit = new Carbon();
        $timestampLimit->subSeconds(30);

        $tweets = \Twitter::getUserTimeline([
            'screen_name' => 'officialmcafee',
            'count' => 6,
            'format' => 'json',
            'exclude_replies' => '1',
            'include_rts' => '0',
        ]);

        $decoded_tweets = json_decode($tweets);

        foreach ($decoded_tweets as $tweet) {
            # Only act on tweets < 1min old, or we risk trigger several buy orders
            $text = $tweet->text;
            $created_at = $tweet->created_at;

            if (property_exists($tweet->entities, 'media')) {
                $image = $tweet->entities->media[0]->media_url;

                $timeOfTweet = new Carbon($created_at);
                if ($timeOfTweet->gte($timestampLimit)) {
                    # Recent tweet!
                    $this->info('Found recent tweet from John McShill!');
                    $this->info('Extracting image ... ');

                    $contents = file_get_contents($image);
                    $name = substr($image, strrpos($image, '/') + 1);
                    Storage::put($name, $contents);

                    $path = 'storage/app/'. $name;

                    $this->info('Doing OCR ... ');
                    $text = \Ocr::recognize($path);
                    $matches = [];
                    preg_match('/\([A-Za-z]{3,4}\)/', $text, $matches);
                    if (array_key_exists(0, $matches)) {
                        $coin = strtoupper($matches[0]);
                        $coin = str_replace(['(', ')'], '', $coin);

                        $this->info('Found '. $coin .', attempting to buy on Bittrex now ... ');

                        # Now ,we have our coin, let's place a buy-order at current price + 10%
                        $market = 'BTC-'. $coin;
                        $currentOrder = Bittrex::getTicker($market);

                        if (is_array($currentOrder)) {
                            if ($currentOrder['success'] == true) {
                                $currentAsk = $currentOrder['result']['Ask'];
                                $myBid = $currentAsk * 1.25; # I tolerate a 25% increase
                                $mySell = $myBid * 1.30;    # Lets aim for 30% profit about our max buy-in price

                                # How many should I buy? Let's do max 0.01BTC
                                # Should probably have 0.015BTC in account at this point.
                                $quantity = (1 / $myBid) * 0.01;

                                $this->info('Buying '. $quantity .' '. $coin .' at a max-rate of '. $myBid .'BTC per coin ... ');

                                # Now, place the buy order
                                $buyResult = Bittrex::buyLimit($market, $quantity, $myBid);
                                if (is_array($buyResult)) {
                                    if ($buyResult['success'] == true) {
                                        # And this better work: set a sell order for +40%
                                        $this->info('Setting sell order in 60s ... ');
                                        sleep(60);
                                        $this->info('Selling '. $quantity .' '. $coin .' at a rate of '. $mySell .'BTC per coin ... ');
                                        $sellResult = Bittrex::sellLimit($market, $quantity, $mySell);
                                        dump($sellResult);

                                        # Done?
                                        $this->info("Done. You are either rich or you've lost it all.");
                                    }
                                }
                            } else {
                                $this->error('Sorry, coin '. $coin .' not found on Bittrex.');
                            }
                        }
                    }
                } else {
                    $this->info('No tweets found in last 60s');
                }
            } else {
                $this->info('No tweets found in last 60s');
            }
        }
    }
}
