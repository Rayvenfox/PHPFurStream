<?php
namespace FurStream
{
    class APIConnection
    {
        private $endpoint = 'https://furstre.am/API/v2/';
        private $key = '';

        public function __construct($key)
        {
            $this->key = $key;
        }

        public function call($interface, $method, $params=array())
        {
            $params['key'] = $this->key;
            $_params = '';
            foreach($params as $k => $v)
            {
                if(is_array($v))
                {
                    $v = implode(',', $v);
                }
                $_params = $_params."$k=$v&";
            }
            $opts = array(
                CURLOPT_URL => $this->endpoint."$interface/$method?$_params",
                CURLOPT_RETURNTRANSFER => true
            );
            $curl = curl_init();
            curl_setopt_array($curl, $opts);
            $response = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if($code != 200)
            {
                throw new APIException($code);
            }
            return json_decode($response);
        }
        public function get_user_summaries($users)
        {
            $resp = $this->call("IUsers", "GetUserSummaries", array('users' => $users));
            $users = array();
            foreach($resp->users as $k)
            {
                array_push($users, new User($k));
            }
            return $users;
        }
        public function get_user_summary()
        {
            return new User($this->call('IUsers', 'GetUserSummary'));
        }
        public function get_stream_summaries($streams)
        {
            $resp = $this->call("IStreams", "GetStreamSummaries", array('streams' => $streams));
            $streams = array();
            foreach($resp->streams as $k)
            {
                array_push($streams, new Stream($k));
            }
            return $streams;
        }
        public function get_stream_summary()
        {
            return new Stream($this->call('IStreams', 'GetStreamSummary'));
        }
        public function get_live_streams()
        {
            $streams = $this->call("IStreams", "GetLiveStreams")->streams;
            return $this->get_stream_summaries($streams);
        }
        public function get_best_server($address)
        {
            return $this->call('IFurStream', 'GetBestServer', array('address' => $address));
        }
    }
    class APIException extends \Exception
    {
        public function __construct($code = 0, Exception $previous = null)
        {
            $message = '';
            if($code === 403)
            {
                $message = "API Key missing or invalid";
            }
            elseif ($code === 400)
            {
                $message = "API Request invalid";
            }
            else
            {
                $message = "Error connecting to the FurStream API";
            }
            parent::__construct($message, $code, $previous);
        }

        public function __toString() 
        {
            return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
        }
    }
    class User
    {
        private $summary;
        public function __construct($summary) 
        {
            $this->summary = $summary;
            foreach($summary as $k => $v)
            {
                $this->$k = $v;
            }
        }
    }
    class Stream
    {
        private $summary;
        public function __construct($summary) 
        {
            $this->summary = $summary;
            foreach($summary as $k => $v)
            {
                $this->$k = $v;
            }
        }
    }
}