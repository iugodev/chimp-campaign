<?php namespace ChimpCampaigns;

use Illuminate\Support\Facades\Facade;

class ChimpCampaignsFacade extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ChimpCampaigns\ChimpCampaigns';
    }
} 