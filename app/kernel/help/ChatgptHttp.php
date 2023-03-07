<?php
namespace app\kernel\help;

use GuzzleHttp\Client;

class ChatgptHttp
{
    protected static int $_index = 0;

    /**
     * 获取内容
     *
     * @param string $model
     * @param string $subject
     * @param string|null $parent_message_id
     * @return array
     */
    public static function getMessage(string $model, string $subject,?string $server = null, ?string $parent_message_id = null): array
    {
        $api = config('app.chatgpt_cluster_'.$model);
        self::$_index = (self::$_index + 1) % count($api);
        $data = [
            'subject' => $subject,
        ];
        if($parent_message_id !== null){
            $data['parent_message_id'] = $parent_message_id;
        }
        
        $client     = new Client();
        $response   = $client->post( $api[$server === null ? self::$_index : $server ], [
            'json'        => $data,
            'timeout'     => 3 * 60 // 3分钟的等待返回时间
        ]);
        
        $body   = json_decode((string) $response->getBody(), true);
        $body['index'] = $server === null ? self::$_index : $server;
        return $body;
    }
}