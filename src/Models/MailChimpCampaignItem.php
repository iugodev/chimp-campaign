<?php namespace ChimpCampaigns\Models;

use Illuminate\Database\Eloquent\Model;

class MailChimpCampaignItem extends Model
{
    protected $table = 'mail_chimp_campaign_items';

    public function campaing() {
        return $this->belongsTo('ChimpCampaigns\Models\MailChimpCampaign');
    }

    public function itemable()
    {
        return $this->morphTo();
    }



}
