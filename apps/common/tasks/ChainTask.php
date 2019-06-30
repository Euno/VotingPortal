<?php
use Phalcon\Cli\Task;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\RawTransaction;

class ChainTask extends Task
{
    //export APPLICATION_ENV=development; php apps/cli.php chain process 20

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

        $startBlock = 438671;
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

        if(isset($tx->vin[0]->scriptSig))
        {
            $asm = explode(' ', $tx->vin[0]->scriptSig->asm);
            $address = Bitcoin::public_key_to_address($asm[1], '00');

            print '===========';
            print($address); // 15L7U55PAsHLEpQkZqz62e3eqWd9AHb2DHyusaku
        }



        return true;
    }
}
