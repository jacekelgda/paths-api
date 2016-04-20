<?php

namespace AppBundle\Controller\Paths\Goals;

use FOS\RestBundle\Controller\FOSRestController;
use Domain\UseCase\EditGoal\Responder;
use Domain\UseCase\EditGoal\Command;
use Domain\Model\Path;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class UpdateController extends FOSRestController implements Responder
{
    private $view;

    /**
     * @ApiDoc(
     *   resource=true,
     *   description="Delete a goal from path",
     *   parameters={
     *     {"name"="userId", "dataType"="string", "required"=true, "description"="path id"},
     *     {"name"="id", "dataType"="string", "required"=true, "description"="goal id"},
     *     {"name"="name", "dataType"="string", "required"=false, "description"="goal name"},
     *     {"name"="description", "dataType"="string", "required"=false, "description"="goal description"},
     *     {"name"="icon", "dataType"="string", "required"=false, "description"="goal icon url"},
     *     {"name"="order", "dataType"="integer", "required"=false, "description"="order number"},
     *     {"name"="dueDate", "dataType"="DateTime", "required"=false, "description"="due date"},
     *     {"name"="achieved", "dataType"="boolean", "required"=false, "description"="goal achieved"},
     *     {"name"="unread", "dataType"="integer", "required"=false, "description"="unread comments count"},
     *     {"name"="level", "dataType"="integer", "required"=false, "description"="goal level"}
     *   }
     * )
     */
    public function putPathsGoalsAction($userId, $id, Request $request)
    {
        $useCase = $this->get('app.use_case.edit_path_goal');

        $command = new Command($userId, $id);
        $command->name = $request->get('name');
        $command->description = $request->get('description');
        $command->icon = $request->get('icon');
        $command->level = $request->get('level');
        $command->order = $request->get('order');
        $command->dueDate = $request->get('dueDate');
        $command->unread = $request->get('unread');
        $command->achieved = $request->get('achieved') == 'true';

        $useCase->execute($command, $this);

        return $this->handleView($this->view);
    }

    public function goalSuccesfullyEdited(Path $path)
    {
        $this->view = $this->view($path);
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
