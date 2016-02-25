<?php namespace ChimpCampaigns;

use ChimpCampaigns\Models\MailChimpCampaign;
use Illuminate\Support\Facades\Config;
use Mailchimp\Mailchimp;

class ChimpCampaigns{


    public function runQuery($action, $data = [], $method = 'GET')
    {
        $api = Config::get('chimpCampaigns.API_KEY');
        $mc = new Mailchimp($api);
        return $mc->request($action, $data, $method);
    }

    //List
    public function getLists()
    {
        return $this->runQuery('lists/', null, "GET");
    }

    public function addListMember($list_id, $email, $extraData)
    {
        $data['email_address'] = $email;
        $data['status'] = 'subscribed';
        $data = array_merge($data, $extraData);

        return $this->runQuery('/lists/'.$list_id.'/members', $data, 'POST');
    }

    public function getCampaignList()
    {
        return $this->runQuery('campaigns', [], "GET");
    }


    //Campaign
    public function createCampaign($list_id, $template, $extraData)
    {
        $data['recipients'] = ['list_id' => $list_id];
        $data['type'] = 'regular';
        $data = array_merge($data, $extraData);

        $this->view = $template;
        $data = $this->runQuery('campaigns/', $data, "POST");


        return $this->createLocalCampaing($data, $template);

    }

    public function find($mailChimpCampaignId)
    {
        return MailChimpCampaign::where('mail_chimp_id', $mailChimpCampaignId)->first();
    }

    public function findOrCreate($mailChimpCampaignId)
    {
        $campaign = $this->find($mailChimpCampaignId);
        if ($campaign == null) {
            $data = $this->getInformation($mailChimpCampaignId);
            $this->createLocalCampaing($data, '');
            return $this->findOrCreate($mailChimpCampaignId);
        }
        return $campaign;
    }

    //Remote Campaign
    public function setContent($mailChimpCampaignId, $plain_text, $extraData = [])
    {
        $campaign = $this->find($mailChimpCampaignId);
        $data['plain_text'] = $plain_text;
        $data['url'] = route('chimpCampaigns.themplate', ['id'=>$campaign->id]);
        $data = array_merge($data, $extraData);

        return $this->runQuery('campaigns/'.$mailChimpCampaignId.'/content', $data, "PUT");
    }

    public function validateCampaign($mailChimpCampaignId)
    {
        $data = $this->getCheckList($mailChimpCampaignId);
        return $data['is_ready'];
    }

    //Local Campaign
    public function addItemCampaing($mailChimpCampaignId, IChimpCampaignItem $item)
    {
        $campaign = $this->findOrCreate($mailChimpCampaignId);
        $campaign->addItem($item);
    }

    public function cleanItems($mailChimpCampaignId)
    {
        $campaign = $this->find($mailChimpCampaignId);
        if ($campaign != null) {
            $this->deleteItemsCampaign($campaign);
        }
    }

    public function deleteLocalCampaing($mailChimpCampaignId)
    {
        $campaign = $this->find($mailChimpCampaignId);
        if ($campaign != null) {
            $this->deleteItemsCampaign($campaign);
            $campaign->delete();
        }
    }


    private function createLocalCampaing($data, $template)
    {
        $campaign = new MailChimpCampaign();

        return $this->updateLocalCampaing($campaign, $data, $template);
    }

    private function updateLocalCampaing(MailChimpCampaign $campaign, $data, $template = null)
    {
        $campaign->mail_chimp_id = $data['id'];
        $campaign->status = $data['status'];
        if ($template != null) {
            $campaign->view = $template;
        }
        $campaign->save();
        return $campaign;
    }

    private function deleteItemsCampaign(MailChimpCampaign $campaign)
    {
        $campaign->items()->delete();
    }


    //API
    public function getInformation($mailChimpCampaignId)
    {
        return $this->runQuery('/campaigns/' . $mailChimpCampaignId);
    }

    public function edit($mailChimpCampaignId, $extraData = [], $template = null)
    {
        $data = json_decode( $this->getInformation($mailChimpCampaignId), true);
        $data = array_merge($data, $extraData);
        $campaign = $this->findOrCreate($mailChimpCampaignId);

        $this->updateLocalCampaing($campaign, $data, $template);

        return $this->runQuery('campaigns/' . $mailChimpCampaignId, $data, "PATCH");
    }

    public function send($mailChimpCampaignId)
    {
        if (! $this->validateCampaign($mailChimpCampaignId)) {
            return false;
        }
        return $this->actions($mailChimpCampaignId, 'send');
    }

    public function test($mailChimpCampaignId, $test_emails = [], $send_type = 'html')
    {
        $data['test_emails'] = $test_emails;
        $data['send_type'] = $send_type;
        return $this->actions($mailChimpCampaignId, 'test', $data);
    }

    public function delete($mailChimpCampaignId)
    {
        $this->runQuery('campaigns/' . $mailChimpCampaignId, null, "DELETE");
        $this->deleteLocalCampaing($mailChimpCampaignId);
    }

    public function getCheckList($mailChimpCampaignId)
    {
        return $this->runQuery('/campaigns/'.$mailChimpCampaignId.'/send-checklist');
    }

    public function actions($mailChimpCampaignId, $action, $data =[])
    {
        return $this->runQuery('campaigns/'.$mailChimpCampaignId.'/actions/'.$action, $data, "POST");
    }


} 