<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Psr7;

class TronsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function transactions(Request $request)
    {
        $client = new \GuzzleHttp\Client();

        $result = [];
        $queryAddress = [];
        $address = $request->input('address');
        $offset = $request->input('offset');
        $limit = $request->input('limit');
        $isError = $request->input('start');
        $start = $request->input('start');
        $end = $request->input('end');
        $addressList[] = $address;
        while(!empty($addressList)){
            $address = array_pop($addressList);
            if(in_array($address, $queryAddress)){
                continue;
            }
            $queryAddress[] = $address;
            $url = 'http://www.oklink.com/api/explorer/v1/tron/transactions';
            $params = [
                't' => 1662449984151,
                'address' => $address,
                'offset' => $offset,
                'limit' => $limit,
                'from' => $address,
                'isError' => 'success'
            ];
            if($isError){
                $params['isError'] = $isError;
            }
            if($start){
                $params['start'] = $start;
            }
            if($end){
                $params['end'] = $end;
            }

            $header = [
                "Accept-Encoding: gzip, deflate, br",
                "Accept-Language: zh-CN",
                "App-Type: web",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Cookie: aliyungf_tc=74a51ef93ce1d4f02b29876b8c66439a8d2726c27d10d56883027cadbf709757; first_ref=https%3A%2F%2Fwww.baidu.com%2Flink%3Furl%3DQ5DfEhZczEkofqyg9m4dlnod72xhDWsq5G6uCglfkTCWYcvgp7hrEXp35RVkFFQU%26wd%3D%26eqid%3Ddd6d629c00001e2f0000000662fdd369; Hm_lvt_5244adb4ce18f1d626ffc94627dd9fd7=1660802202; _okcoin_legal_currency=CNY; locale=zh_CN; Hm_lpvt_5244adb4ce18f1d626ffc94627dd9fd7=1662449984",
                "devId: 5c982f7c-f0c3-406a-896d-86e419f83a33",
                "Host: www.oklink.com",
                "Pragma: no-cache",
                "Referer: https://www.oklink.com/zh-cn/trx/address/TM1zzNDZD2DPASbKcgdVoTYhfmYgtfwx9R",
                'sec-ch-ua: "Chromium";v="104", " Not A;Brand";v="99", "Google Chrome";v="104"',
                "sec-ch-ua-mobile: ?0",
                'sec-ch-ua-platform: "macOS"',
                "Sec-Fetch-Dest: empty",
                "Sec-Fetch-Mode: cors",
                "Sec-Fetch-Site: same-origin",
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
                "x-apiKey" => "LWIzMWUtNDU0Ny05Mjk5LWI2ZDA3Yjc2MzFhYmEyYzkwM2NjfDI3NzM1NjEwOTUyMzM3NDI=",
                "x-cdn: https://static.oklink.com",
                "x-utc: 8"
            ];
            $array = [
                'headers' => $header,
                'query' => $params,
                'http_errors' => false   #支持错误输出
            ];
            $response = $client->request('GET', $url, $array);
            $data = json_decode($response->getBody()->getContents(),true);
            $data = $data['data']['hits']??[];
            if(empty($data)){
                continue;
            }
            foreach($data as $value){
                $result[$value['from'][0]] = $data;
                $addressList[] = $value['to'][0];
            }
        }

        return json_encode($result);
    }
}
