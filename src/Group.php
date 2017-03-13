<?php

namespace HangoutsGroups;

class Group
{
    private $link;
    private $cookies;

    function __construct(string $link, string $cookies)
    {
        $this->link = $link;
        $this->cookies = $cookies;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    private function fixJson(string $invalidJson): string
    {
        $validJson = [];
        $elems = explode(',', $invalidJson);
        foreach ($elems as $elem) {
            $validJson[] = mb_strlen(trim($elem)) == 0 ? 'null' : $elem;
        }

        return implode(',', $validJson);
    }

    public function getMembers(): array
    {

        $members = [];

        $client = new \GuzzleHttp\Client();

        $res = $client->request('GET', $this->link, ['headers' => [
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'accept-encoding' => 'gzip, deflate, lzma, sdch, br',
            'accept-language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
            'cache-control' => 'max-age=0',
            'cookie' => $this->cookies,
            'upgrade-insecure-requests' => '1',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2729.0 Safari/537.36'
        ]]);

        $body = $res->getBody()->getContents();

        $normalizeString = function ($string) {
            return preg_replace_callback('/\\\\x([0-9a-f]{2})/', function ($m) {
                return chr(hexdec($m['1']));
            }, $string);
        };

        if (preg_match('/ho_sr\(\"([^"]+)\"\)/i', $body, $m) AND preg_match('/window.HO_CSIID = "([^"]+)";/is', $body, $m2)) {

            $link = 'https://hangouts.google.com' . $normalizeString($m['1']);
            $body = '{"hsc":["' . basename($this->link) . '",0,null,null,null,[],null,null,null,0,0,51,null,0,null,null,0,null,[],null,null,null,null,null,null,null,null,0,[],[],null,null,null,[],null,null,null,[],null,null,[],"' . $m2['1'] . '"]}';

            $res = $client->request('POST', $link, ['headers' => [
                'accept' => '*/*',
                'accept-encoding' => 'gzip, deflate, lzma, br',
                'accept-language' => 'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
                'content-length' => strlen($body),
                'cookie' => $this->cookies,
                'content-type' => 'text/plain;charset=UTF-8',
                'origin' => 'https://hangouts.google.com',
                'referer' => $this->link,
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2729.0 Safari/537.36'
            ], 'body' => $body]);

            if ($arrayResp = json_decode($this->fixJson($res->getBody()->getContents()), true) and isset($arrayResp['0']['2']['0']['6'])) {
                foreach ($arrayResp['0']['2']['0']['6'] as $member) {
                    $members[] = [
                        'id' => $member['4'],
                        'name' => $member['0'],
                        'picture' => $member['1']
                    ];
                }
            }
        }

        return $members;
    }
}