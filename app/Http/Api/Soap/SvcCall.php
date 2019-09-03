<?php
namespace App\Http\Api\Soap;

use App\Models\StaticState;
use App\Services\ApiLogService;
use Illuminate\Support\Facades\Log;
use SoapClient;

class SvcCall
{
    protected $_appToken;
    protected $_appKey;
    protected $wsdl;
    protected $_client = null;
    protected $request;

    /**
     * SvcCall constructor.
     * @param array $requestParam array(command, params)
     */
    public function __construct($requestParam)
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        $this->wsdl = config('api.app_model') ? config('api.wmsOms.appUrl') : config('api.wmsOms.testAppUrl');
        $this->_appToken = config('api.wmsOms.appToken');
        $this->_appKey = config('api.wmsOms.appKey');

        $request = [];
        $request['wsdl'] = $this->wsdl;
        $request['appToken'] = $this->_appToken;
        $request['appKey'] = $this->_appKey;
        $this->request = array_merge($request, $requestParam);
    }

    private function getClient()
    {
        $this->setClient() ;
        return $this->_client;
    }

    private function setClient()
    {
        $omsConfig = array(
            'active' => '1',
            'timeout' => '3000'
        );

        libxml_disable_entity_loader(false);
        //超时
        $timeout = isset($omsConfig['timeout']) && is_numeric($omsConfig['timeout']) ? $omsConfig['timeout'] : 1000;
        
        $options = array(
            "trace" => true,
            "connection_timeout" => $timeout,
            "encoding" => "utf-8" ,
            'ssl'   => array(
                'verify_peer' => false
            ),
            'https' => array(
                'curl_verify_ssl_peer' => false,
                'curl_verify_ssl_host' => false
            )
        );

        try {
            $url = $this->wsdl ;
            $client = new SoapClient($url, $options);
            $this->_client = $client;
            unset($client);
        } catch (\Exception $e) {
            $saveLogs = [];
            $saveLogs['api_type'] = StaticState::API_TYPE_PULL;
            $saveLogs['api_name'] = 'Soap接口';
            $saveLogs['is_success'] = 0;
            $saveLogs['run_start_time'] = null;
            $saveLogs['run_end_time'] = null;
            $saveLogs['request_parameter'] = json_encode($this->request);
            $saveLogs['response_result'] = $e->getMessage();
            ApiLogService::doCreate($saveLogs);
        }
    }

    /**
     * 调用webservice
     * @param unknown_type $req            
     * @return Ambigous <mixed, NULL, multitype:, multitype:Ambigous <mixed,
     *         NULL> , StdClass, multitype:Ambigous <mixed, multitype:,
     *         multitype:Ambigous <mixed, NULL> , NULL> , boolean, number,
     *        string, unknown>
     */
    private function callService($req)
    {
        $client = $this->getClient();
        if (empty($client)) {
            $return = [
                'Ask' => 'Failure',
                'Message' => 'Failure'
            ];
            return $return;
        }

        $req['appToken'] = $this->_appToken;
        $req['appKey'] = $this->_appKey;

        $result = $client->callService($req);
        $result = Common::objectToArray($result);
        $return = json_decode($result['response']);
        $return = Common::objectToArray($return);

        return $return;
    }

    /**
     * 通用接口调用命令
     * @param string $command 接口方法名称
     * @param array|null  $params  接口方法参数
     * @return array
     */
    public static function remoteCommand($command, $params = null)
    {
        $return = array(
            'Ask' => 'Failure',
            'Message' => ''
        );

        $req = array(
            'service' => $command,
            'paramsJson' => json_encode($params)
        );

        $requestParam = [];
        $requestParam['command'] = $command;
        $requestParam['params'] = $params;
        $return =(new static($requestParam))->callService($req);

        return $return;
    }

}