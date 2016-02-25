<?php

namespace ChimpCampaigns\Models;

use Illuminate\Database\Eloquent\Model;
use ChimpCampaigns\IChimpCampaignItem;


class MailChimpCampaign extends Model
{
    public function items()
    {
        return $this->hasMany('ChimpCampaigns\Models\MailChimpCampaignItem');
    }

    public function getExtraData()
    {
        return json_decode($this->extra_data);
    }

    public function addItem(IChimpCampaignItem $item) {

        $mailItem = new MailChimpCampaignItem();
        $mailItem->mail_chimp_campaign_id = $this->id;

        $item->mailChimpCampaignItem()->save($mailItem);
    }

}
