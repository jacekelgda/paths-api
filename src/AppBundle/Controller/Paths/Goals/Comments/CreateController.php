<?php

namespace AppBundle\Controller\Paths\Goals\Comments;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Domain\UseCase\AddComment\Responder;
use Domain\UseCase\AddComment\Command;
use Domain\Model\Goal;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateController extends FOSRestController implements Responder
{
    private $view;

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Create a comment",
     *   parameters={
     *     {"name"="userId", "dataType"="string", "required"=true, "description"="user id"},
     *     {"name"="goalId", "dataType"="string", "required"=true, "description"="goal id"},
     *     {"name"="author", "dataType"="string", "required"=true, "description"="author"},
     *     {"name"="text", "dataType"="string", "required"=true, "description"="text"},
     *     {"name"="replyTo", "dataType"="string", "required"=false, "description"="id of comment to reply"}
     *   }
     * )
     */
    public function postPathsGoalsCommentsAction($userId, $goalId, Request $request)
    {
        $useCase = $this->get('app.use_case.add_path_goal_comment');

        $author = $request->get('author');
        $text = $request->get('text');
        $replyTo = $request->get('replyTo');

        if (empty($author) || empty($text)) {
            throw new HttpException(400, 'Missing required parameters');
        }

        $command = new Command($userId, $goalId, $author, $text);
        $command->replyTo = $replyTo;

        $useCase->execute($command, $this);

        return $this->handleView($this->view);
    }

    public function commentSuccesfullyAdded(Goal $goal)
    {
        $this->view = $this->view($goal);
    }

    public function pathNotFound($userId)
    {
        throw $this->createNotFoundException('Path does not exist');
    }

    public function goalNotFound($goalId)
    {
        throw $this->createNotFoundException('Goal does not exist');
    }
}