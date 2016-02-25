<?php

trait ChimpCamaignsTrait {

    public function mailChimpCampaignItem()
    {
        return $this->morphMany('ChimpCampaigns\Models\MailChimpCampaignItem', 'itemable');
    }
} 