<?php
use Phalcon\Cli\Task;

class NodesTask extends Task
{
    //export APPLICATION_ENV=development; php apps/cli.php nodes

    public function mainAction()
    {
        $config = $this->getDI()->get('config');

        $connect_string = sprintf('http://%s:%s@%s:%s/', $config->eunod->user, $config->eunod->pass, $config->eunod->host, $config->eunod->port);
        $coind = new EunoVoting\Common\Libraries\jsonRPCClient($connect_string);

        $nodes = $coind->masternode('list', 'pubkey');

        if(is_array($nodes))
        {
            $nodes = array_values($nodes);

            foreach (\EunoVoting\Common\Models\GovernanceMembers::find('deleted = 0') as $member)
            {
                if(in_array($member->masternode_address, $nodes))
                {
                    $member->last_seen = time();
                    $member->update();
                }
            }
        }
    }
}