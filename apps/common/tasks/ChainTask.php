<?php
use Phalcon\Cli\Task;

class ChainTask extends Task
{
    protected $rpc;

    public function initialize()
    {
        $config = $this->getDI()->get('config');

        $connect_string = sprintf('http://%s:%s@%s:%s/', $config->eunod->user, $config->eunod->pass, $config->eunod->host, $config->eunod->port);
        $this->rpc = new EunoVoting\Common\Libraries\jsonRPCClient($connect_string);
    }

    public function processAction($params = [])
    {
        $maxBlocks = $params[0] ?? 0;

        set_time_limit(2);
        $countBlocks = $this->rpc->getblockcount();

        set_time_limit(0);
        if(!isset($countBlocks) || is_null($countBlocks) || !is_int($countBlocks))
        {
            echo "Can't get block count".PHP_EOL;
            die();
        }

        echo "Number of EUNO blocks: ".$countBlocks.PHP_EOL;

        $startBlock = 308620;
        $processedBlock = 0;
        for($nBlock=$startBlock;$nBlock<=$countBlocks;$nBlock++)
        {
            echo "Processing block ".$nBlock.".....".PHP_EOL;

            //Process block
            $this->_processBlock($nBlock);

            $processedBlock++;

            if($maxBlocks > 0 && $processedBlock == $maxBlocks)
                break;
        }
    }

    protected function _processBlock($nBlock = 0)
    {
        if($nBlock < 1)
            return false;

        set_time_limit(2);
        $block = $this->rpc->getblockbynumber($nBlock);

        set_time_limit(0);

        print_r($block);

        foreach ($block['tx'] as $tx)
        {
            echo "Processing transaction ".$tx.".....".PHP_EOL;
            $this->_processTransaction($tx);
        }
    }

    protected function _processTransaction($txHash = '')
    {
        if(!$txHash)
            return false;

        set_time_limit(2);
        $tx = $this->rpc->gettransaction($txHash);

        set_time_limit(0);

        $tx = json_decode(json_encode($tx));

        print_r($tx);


        //return;

        //returns tx value
        //$txhash=$tx->hash;
        //$txsize=$tx->size;
        //$rawtx = $this->indent(json_encode($tx));

        foreach($tx->vin as $input)
        {
            $type=NULL;
            $prev=NULL;
            $previndex=NULL;
            $hash160=NULL;
            $scriptsig=NULL;
            $index=NULL;
            $value=NULL;

            //echo "INPUT\n";
            if(isset($input->coinbase))
            {
                $type="Generation";
                //$value=bcdiv("50",floor(pow(2,floor($blocknum/210000))),8);
                $scriptsig=$input->coinbase;

            }
            else
            {
                //$prev=$input->txid;
                //$index=$input->n;
                //scriptsig=$input->scriptSig;
                //$simplescriptsig=$this->simplifyscript($scriptsig);
                //echo "Simplescriptsig: ".$simplescriptsig."$\n";

                $hex = $input->scriptSig->hex;
                $hexDecode = $this->decodeHex($hex);
                $hash160 = strtolower($this->hash160($hex));
                $address = $this->hash160ToAddress($hash160);

                echo $hexDecode.PHP_EOL;
                echo $hash160.PHP_EOL;
                echo $address.PHP_EOL;

                /*$prevtx=pg_fetch_assoc(pg_query_params($db, "SELECT value,type,encode(hash160,'hex') AS hash160 FROM outputs WHERE index=$1 AND tx=decode($2,'hex');",array($index,$prev)));
                if(!$prevtx)§
                {
                    var_dump(shell_exec("crontab -r"));
                    die("Error: Failed getting prev tx...");
                }
                $value=$prevtx["value"];
                $type=$prevtx["type"];
                $hash160=$prevtx["hash160"];
                if($type=="Address")
                {
                    if(preg_match("/^[0-9a-f]+ [0-9a-f]{66,130}$/",$simplescriptsig))
                    {
                        $pubkey=preg_replace("/^[0-9a-f]+ ([0-9a-f]{66,130})$/","$1",$simplescriptsig);
                        $hash160=strtolower($this->hash160($pubkey));
                    }
                }*/
            }

            /*echo "Type: ".$type."$\n";
            echo "Value: ".$value."$\n";
            echo "Prev: ".$prev."$\n";
            echo "TxHash: ".$txHash."$\n";
            echo "Index: ".$index."$\n";
            echo "ScriptSig: ".$scriptsig."$\n";
            echo "Hash160: ".$hash160."$\n";*/
        }

        /*$index=-1;
        $txvalue="0";
        foreach($tx->vout as $output)
        {
            $hash160=NULL;
            $type=NULL;
            $index++;
            echo "OUTPUT\n";
            $value=$output->value;
            $txvalue=bcadd($txvalue,$value,8);
            $scriptpubkey=$output->scriptPubKey;
            $simplescriptpk=simplifyscript($scriptpubkey);
            echo "Simplescriptpubkey: ".$simplescriptpk."$\n";

            //To pubkey
            if(preg_match("/^[0-9a-f]{66,130} OP_CHECKSIG$/",$simplescriptpk))
            {
                $type="Pubkey";
                $pubkey=preg_replace("/^([0-9a-f]{66,130}) OP_CHECKSIG$/","$1",$simplescriptpk);
                $hash160=strtolower(hash160($pubkey));
                updateKeys($hash160,$pubkey,$blockhash);
            }

            //To BC address
            if(preg_match("/^OP_DUP OP_HASH160 [0-9a-f]{40} OP_EQUALVERIFY OP_CHECKSIG$/",$simplescriptpk))
            {
                $type="Address";
                $hash160=preg_replace("/^OP_DUP OP_HASH160 ([0-9a-f]{40}) OP_EQUALVERIFY OP_CHECKSIG$/","$1",$simplescriptpk);
                updateKeys($hash160,NULL,$blockhash);
            }

            if(is_null($type))
            {
                $type="Strange";
            }

            echo "Hash160: ".$hash160."$\n";
            echo "Type: ".$type."$\n";
            echo "Index: ".$index."$\n";
            echo "Value: ".$value."$\n";
            echo "Scriptpubkey: ".$scriptpubkey."$\n";
        }*/

    }

    protected function decodeHex($hex)
    {
        $hex=strtoupper($hex);
        $chars="0123456789ABCDEF";
        $return="0";
        for($i=0;$i<strlen($hex);$i++)
        {
            $current=(string)strpos($chars,$hex[$i]);
            $return=(string)bcmul($return,"16",0);
            $return=(string)bcadd($return,$current,0);
        }
        return $return;
    }

    protected function encodeHex($dec)
    {
        $chars="0123456789ABCDEF";
        $return="";
        while (bccomp($dec,0)==1)
        {
            $dv=(string)bcdiv($dec,"16",0);
            $rem=(integer)bcmod($dec,"16");
            $dec=$dv;
            $return=$return.$chars[$rem];
        }
        return strrev($return);
    }

    protected function decodeBase58($base58)
    {
        $origbase58=$base58;

        $chars="123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
        $return="0";
        for($i=0;$i<strlen($base58);$i++)
        {
            $current=(string)strpos($chars,$base58[$i]);
            $return=(string)bcmul($return,"58",0);
            $return=(string)bcadd($return,$current,0);
        }

        $return=$this->encodeHex($return);

        //leading zeros
        for($i=0;$i<strlen($origbase58)&&$origbase58[$i]=="1";$i++)
        {
            $return="00".$return;
        }

        if(strlen($return)%2!=0)
        {
            $return="0".$return;
        }

        return $return;
    }

    protected function encodeBase58($hex)
    {
        if(strlen($hex)%2!=0)
        {
            die("encodeBase58: uneven number of hex characters");
        }
        $orighex=$hex;

        $chars="123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
        $hex=$this->decodeHex($hex);
        $return="";
        while (bccomp($hex,0)==1)
        {
            $dv=(string)bcdiv($hex,"58",0);
            $rem=(integer)bcmod($hex,"58");
            $hex=$dv;
            $return=$return.$chars[$rem];
        }
        $return=strrev($return);

        //leading zeros
        for($i=0;$i<strlen($orighex)&&substr($orighex,$i,2)=="00";$i+=2)
        {
            $return="1".$return;
        }

        return $return;
    }

    protected function hash160ToAddress($hash160,$addressversion='0x21')
    {
        $hash160=$addressversion.$hash160;
        $check=pack("H*" , $hash160);
        $check=hash("sha256",hash("sha256",$check,true));
        $check=substr($check,0,8);
        $hash160=strtoupper($hash160.$check);
        return $this->encodeBase58($hash160);
    }

    protected function addressToHash160($addr)
    {
        $addr=$this->decodeBase58($addr);
        $addr=substr($addr,2,strlen($addr)-10);
        return $addr;
    }

    protected function checkAddress($addr,$addressversion='0x21')
    {
        $addr=$this->decodeBase58($addr);
        if(strlen($addr)!=50)
        {
            return false;
        }
        $version=substr($addr,0,2);
        if(hexdec($version)>hexdec($addressversion))
        {
            return false;
        }
        $check=substr($addr,0,strlen($addr)-8);
        $check=pack("H*" , $check);
        $check=strtoupper(hash("sha256",hash("sha256",$check,true)));
        $check=substr($check,0,8);
        return $check==substr($addr,strlen($addr)-8);
    }

    protected function hash160($data)
    {
        $data=pack("H*" , $data);
        return strtoupper(hash("ripemd160",hash("sha256",$data,true)));
    }

    protected function pubKeyToAddress($pubkey)
    {
        return $this->hash160ToAddress($this->hash160($pubkey));
    }

    protected function remove0x($string)
    {
        if(substr($string,0,2)=="0x"||substr($string,0,2)=="0X")
        {
            $string=substr($string,2);
        }
        return $string;
    }

    protected function simplifyscript($script)
    {
        $script=preg_replace("/[0-9a-f]+ OP_DROP ?/","",$script);
        $script=preg_replace("/OP_NOP ?/","",$script);
        return trim($script);
    }

    protected function indent($json) {
        $result    = '';
        $pos       = 0;
        $strLen    = strlen($json);
        $indentStr = '  ';
        $newLine   = "\n";
        for($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string
            $char = substr($json, $i, 1);

            // If this character is the end of an element,
            // output a new line and indent the next line
            if($char == '}' || $char == ']') {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string
            $result .= $char;
            // If the last character was the beginning of an element,
            // output a new line and indent the next line
            if ($char == ',' || $char == '{' || $char == '[') {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
        }

        return $result;
    }
}