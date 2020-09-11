<?php

namespace App\Controller;

use App\Entity\Task;

use App\Form\TaskType;

use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     * @param TaskRepository $taskRepository
     * @return Response
     */
    public function listAction(TaskRepository $taskRepository):Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
    }
    /**
     * @Route("/tasks_done", name="task_list_done")
     * @param TaskRepository $taskRepository
     * @return Response
     */
     public function listDone(TaskRepository $taskRepository):Response
     {

        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone'=>true])]);
     }
    /**
     * @Route("/tasks/create", name="task_create")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request,EntityManagerInterface $manager)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setUser($this->getUser());
            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @param Task $task
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     * @Security ("(is_granted('ROLE_USER') and user === task.getUser()) or is_granted('ROLE_ADMIN')")
     */
    public function editAction(Task $task, Request $request,EntityManagerInterface $manager)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     * @param Task $task
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function toggleTaskAction(Task $task,EntityManagerInterface $manager)
    {
        $task->toggle(!$task->isDone());
        $manager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @param Task $task
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     * @Security ("(is_granted('ROLE_USER') and user === task.getUser()) or is_granted('ROLE_ADMIN')")
     */
    public function deleteTaskAction(Task $task,EntityManagerInterface $manager)
    {
        $manager->remove($task);
        $manager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
