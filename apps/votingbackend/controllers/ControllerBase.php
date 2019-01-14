<?php
namespace EunoVoting\VotingBackend\Controllers;

use EunoVoting\Common\Libraries\jsonRPCClient;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        // Add some local CSS resources
        $this->assets
            ->addCss('assets/plugins/switchery/switchery.min.css')
            ->addCss('assets/plugins/datatables/dataTables.bootstrap4.min.css')
            ->addCss('assets/plugins/datatables/buttons.bootstrap4.min.css')
            ->addCss('assets/plugins/datatables/responsive.bootstrap4.min.css')
            ->addCss('assets/plugins/bootstrap-daterangepicker/daterangepicker.css')
            ->addCss('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')
            ->addCss('assets/plugins/select2/css/select2.css')
            ->addCss('assets/css/style.css')
            ->addCss('assets/css/custom.css')
            ;

        // And some local JavaScript resources
        $this->assets
            ->addJs('assets/js/jquery.min.js')
            ->addJs('assets/js/tether.min.js')
            ->addJs('assets/js/bootstrap.min.js')
            ->addJs('assets/js/waves.js')
            ->addJs('assets/js/jquery.nicescroll.js')
            ->addJs('assets/plugins/switchery/switchery.min.js')
            ->addJs('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')

            ->addJs('assets/plugins/morris/morris.min.js')
            ->addJs('assets/plugins/raphael/raphael-min.js')

            ->addJs('assets/plugins/waypoints/lib/jquery.waypoints.js')
            ->addJs('assets/plugins/counterup/jquery.counterup.min.js')

            ->addJs('assets/js/jquery.core.js')
            ->addJs('assets/plugins/moment/moment.js')
            ->addJs('assets/plugins/datatables/jquery.dataTables.min.js')
            ->addJs('assets/plugins/datatables/dataTables.bootstrap4.min.js')
            ->addJs('assets/plugins/bootstrap-daterangepicker/daterangepicker.js')
            ->addJs('assets/plugins/select2/js/select2.full.js')
            ->addJs('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')
            ;


        $connect_string = sprintf('http://%s:%s@%s:%s/', $this->config->eunod->user, $this->config->eunod->pass, $this->config->eunod->host, $this->config->eunod->port);
        $coind = new jsonRPCClient($connect_string);

        $info = $coind->getinfo();

        $version = 'Offline';
        $status = false;
        if(isset($info['version']))
        {
            $status = true;
            $version = $info['version'];
        }

        $this->view->wallet_status = $status;
        $this->view->wallet_version = $version;
    }
}