<?php
use Phalcon\Mvc\View;

class VotingsController extends ControllerBase
{
    public function indexAction()
    {
        $votings = Votings::find('round = 1');

        $this->view->votings = $votings;
    }

    public function editAction($id = 0)
    {
        $answers = [];
        if($id)
        {
            $voting = Votings::findFirst($id);

            if(!$voting)
            {
                return $this->response->redirect('/');
            }

            $answers = $voting->getAnswers();
        }
        else
        {
            $voting = new Votings();
        }

        $this->view->voting = $voting;
        $this->view->answers = $answers;
    }

    public function saveAction($id = 0)
    {
        $this->view->disable();

        if($this->request->isPost())
        {
            $post = $this->request->getPost();

            if($id)
            {
                $voting = Votings::findFirst($id);
                $voting->getAnswers()->delete();
            }
            else
            {
                $voting = new Votings();
            }

            $voting->title = $post['title'];
            $voting->url = $post['url'];
            $voting->end_date = strtotime($post['end_date']);
            $voting->save();

            foreach ($post['answers'] as $answer)
            {
                if(trim($answer))
                {
                    $ans = new VotingsAnswers();
                    $ans->voting_id = $voting->id;
                    $ans->answer = trim($answer);
                    $ans->create();
                }
            }
        }

        return $this->response->redirect('votings/edit/'.$voting->id);
    }

    public function deleteAction($id = 0)
    {
        if($id)
        {
            $voting = Votings::findFirst($id);

            if($voting)
            {
                $voting->getAnswers()->delete();
                $voting->delete();
            }
        }

        return $this->response->redirect('/votings');
    }

    public function addSecondAction($voting_id = 0 )
    {
        $voting = Votings::findFirst($voting_id);

        $second = new Votings();
        $second->title = $voting->title.' (Final voting)';
        $second->url = $voting->url.'-final-voting';
        $second->parent = $voting_id;
        $second->round = 2;
        $second->create();

        $answers = $voting->getAnswers();

        foreach ($answers as $a)
        {
            $nAnswer = new VotingsAnswers();
            $nAnswer->voting_id = $second->id;
            $nAnswer->answer = $a->answer;
            $nAnswer->create();
        }

        return $this->response->redirect('votings/edit/'.$second->id);
    }

    public function votesAction($voting_id = 0)
    {
        $voting = Votings::findFirst($voting_id);

        if(!$voting)
            return $this->response->redirect('votings');

        $this->view->voting = $voting;
        $this->view->votes = $voting->getVotes();
    }

    public function approveAction($voting_id = 0, $vote_id = 0)
    {
        $voting = Votings::findFirst($voting_id);

        if($voting)
        {
            $vote = Votes::findFirst($vote_id);

            if($vote)
            {
                $vote->confirmed = 1;
                $vote->update();
            }
        }

        return $this->response->redirect('votings/votes/'.$voting->id);
    }

    public function declineAction($voting_id = 0, $vote_id = 0)
    {
        $voting = Votings::findFirst($voting_id);

        if($voting)
        {
            $vote = Votes::findFirst($vote_id);

            if($vote)
            {
                $vote->confirmed = 2;
                $vote->update();
            }
        }

        return $this->response->redirect('votings/votes/'.$voting->id);
    }

    public function reportAction($voting_id = 0)
    {
        $this->view->disable();

        $voting = Votings::findFirst($voting_id);

        if(!$voting)
        {
            return $this->response->redirect('votings');
        }

        $mpdf = new \Mpdf\Mpdf();

        $view = new View();
        $view->disableLevel(
            View::LEVEL_MAIN_LAYOUT
        );
        $view->setViewsDir(__DIR__ . '/../../app/views/');

        $html = $view->getRender('votings', 'renderPdf', []);

        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($html);
        $mpdf->Output('', "I");
    }

    public function reportBarsAction($voting_id = 0)
    {
        $chart = new Barchart();
        echo $chart->draw();
        exit;
    }
}