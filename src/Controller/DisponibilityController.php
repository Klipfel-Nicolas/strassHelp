<?php

namespace App\Controller;

use App\Model\DisponibilityManager;

class DisponibilityController extends AbstractController
{
    public function index(): string
    {
        $disponibilityManager = new DisponibilityManager();
        $disponibilitys = $disponibilityManager->selectAll('timetable');

        $this->restrictAdmin();
        return $this->twig->render('Disponibility/index.html.twig', ['disponibilitys' => $disponibilitys]);
    }

    public function edit(int $id): string
    {
        $disponibilityManager = new DisponibilityManager();
        $disponibility = $disponibilityManager->SelectOneById($id);
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $disponibility = array_map('trim', $_POST);
            $disponibilityManager->update($disponibility);
            header('Location:/Disponibility/index');
        }

        $this->restrictAdmin();
        return $this->twig->render('Disponibility/edit.html.twig', ['disponibility' => $disponibility]);
    }

    public function add(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $disponibility = array_map('trim', $_POST);
            $disponibilityManager = new DisponibilityManager();
            $disponibilityManager->insert($disponibility);
            header('Location:/Disponibility/index');
        }
        $this->restrictAdmin();
        return $this->twig->render('Disponibility/add.html.twig');
    }

    public function delete(int $id)
    {
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $disponibilityManager = new DisponibilityManager();
            $disponibilityManager->delete($id);
            header('Location:/Disponibility/index');
        }
    }
}
