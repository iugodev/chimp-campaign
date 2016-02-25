<?php namespace ChimpCampaigns\Controllers;



use Illuminate\Routing\Controller as Controller;
use ChimpCampaigns\Models\MailChimpCampaign;


class MailChimpThemplateController extends Controller
{
    protected $mailChimpCampaign;

    function __construct(MailChimpCampaign $mailChimpCampaign)
    {
        $this->mailChimpCampaign = $mailChimpCampaign;
    }

    public function getIndex($id) {
        $camp = $this->mailChimpCampaign->find($id);

        $view = $camp->view;
        $items = $camp->items;
        $data = $camp->getExtraData();


        return view($view, compact('items', 'data'));
    }


}
