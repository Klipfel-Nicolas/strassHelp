<?php

namespace App\Controller;

use App\Model\CategoryManager;

class CategoryController extends AbstractController
{
    public function index(): string
    {
        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        $this->restrictAdmin();
        return $this->twig->render('Category/index.html.twig', ['categories' => $categories]);
    }

    public function edit(int $id): string
    {
        $categoryManager = new CategoryManager();
        $category = $categoryManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = array_map('trim', $_POST);

            $categoryManager->update($category);
            header('Location: /category/index');
        }

        $this->restrictAdmin();
        return $this->twig->render('Category/edit.html.twig', ['category' => $category,]);
    }

    public function add(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category = array_map('trim', $_POST);

            $categoryManager = new CategoryManager();
            $categoryManager->insert($category);
            header('Location: /category/index');
        }
        $this->restrictAdmin();
        return $this->twig->render('Category/add.html.twig');
    }

    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoryManager = new CategoryManager();
            $categoryManager->delete($id);
            header('Location:/category/index');
        }
    }
}
