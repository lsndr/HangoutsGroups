<?php

namespace HangoutsGroups;

class GroupsManager
{
    private $cookies;

    function __construct(string $cookies)
    {
        $this->cookies = $cookies;
    }

    public function create(): ?Group
    {
        $client = new \GuzzleHttp\Client([
            'headers' => [
                'cookie' => $this->cookies
            ]
        ]);

        $res = $client->request('GET', 'https://plus.google.com/hangouts/_?ssc=' . base64_encode('["",0,null,null,null,[],null,null,null,null,null,0,null,null,null,[0],null,null,[],null,"0",null,null,null,null,null,null,null,[],[],null,null,null,[],null,null,null,[],null,null,[["184219133185","dQw4w9WgXcQ",2]]]'));
        return preg_match('/window\.location\.href = "(https\:\/\/hangouts\.google\.com\/hangouts\/\_\/[^"]+)";/is', $res->getBody()->getContents(), $m) ? new Group($m['1'], $this->cookies) : null;
    }


}