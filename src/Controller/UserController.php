<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\AdvertManager;
use App\Model\AdvertHelpManager;
use App\Controller\CheckForm;
use App\Controller\AuthController;
use App\Model\AuthManager;
use App\Model\CategoryManager;
use App\Model\DisponibilityManager;
use App\Model\ReviewManager;

class UserController extends AbstractController
{
    /**
    * secure form
    */
    public function validData($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function checkUserForm(): array
    {
        $checkForm = new CheckForm();
        $errors = [
            $checkForm->displayEmptyErrors(),
            $checkForm->displayAgeErrors(),
            $checkForm->displayMailPasswodErrors(),
            $checkForm->displayAvatarErrors()
        ];

        $allErrors = array();

        foreach ($errors as $value) {
            if ($value > 0 || $value !== null) {
                $allErrors = array_merge($allErrors, $value);
            }
        }
        return $allErrors;
    }

    public function uploadAvatar(array $userArray)
    {
        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            $tailleMax = 2097152;
            $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
            if ($_FILES['avatar']['size'] <= $tailleMax) {
                $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
                if (in_array($extensionUpload, $extensionsValides)) {
                    $avatarName = uniqid() . "." . $extensionUpload;
                    $avatarPath = "assets/images/membres/avatars/" . $avatarName;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $avatarPath);
                    $userArray['avatar'] = $avatarName;
                }
            }
        }
        return $userArray;
    }

    /**
     * CRUD User
     */

    /**
     * List of all Users
     */
    public function allUsers()
    {
        $userManager = new UserManager();
        $allUser = $userManager->selectAll();

        $this->restrictLogIn();
        return $this->twig->render('User/allUserShow.html.twig', ['user' => $allUser]);
    }

    /**
     * Show profil for a specific user
     */
    public function userShow(int $id)
    {
        $userManager = new UserManager();
        $advertManager = new AdvertManager();
        $helpManager = new AdvertHelpManager();
        $reviewsManager = new ReviewManager();
        $disponibilityManager = new DisponibilityManager();
        $categoryManager = new CategoryManager();

        $user = $userManager->selectOneById($id);
        $advert = $advertManager->selectByUserId($id);
        $help = $helpManager->selectAllHelpByUser($id);
        $rate = $reviewsManager->averageByUser($id);
        $disponibility = $disponibilityManager->selectAll();
        $category = $categoryManager->selectAll();

        // GROUPER LES HELP PAR ID_CHAT
        $group = array();
        foreach ($help as $data) {
            $group[$data['id_chat']][] = $data;
        }
        
        $this->restrictLogIn();
        return $this->twig->render('User/userShow.html.twig', [
                    'user' => $user,
                    'advert' => $advert,
                    'help' => $group,
                    'rate' => $rate,
                    'category' => $category,
                    'dispo' => $disponibility
        ]);
    }

    /**
     * Add a new user
     */
    public function add(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $userDatas = array_map(array($this, "validData"), $_POST);
            // if validation is ok, insert and redirection
            if (count($this->checkUserForm()) == 0) {
                //hash Password
                $userDatas['password'] = password_hash($userDatas['password'], PASSWORD_BCRYPT);
                $userDatas = $this->uploadAvatar($userDatas);
                $userManager = new UserManager();
                $userManager->insert($userDatas);
                $autController = new AuthController();
                $autController->log();
            } else {
                return $this->twig->render('User/add.html.twig', [
                    'user' => $userDatas,
                    'errors' => $this->checkUserForm()
                ]);
            }
        }
            return $this->twig->render('User/add.html.twig');
    }

    /**
     * Delete a specific user
     */
    public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userManager = new UserManager();
            $userManager->delete($id);
            header('Location:/user/allUsers');
        }
    }

    /**
     * Delete own count
     */
    public function deleteSelf(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userManager = new UserManager();
            $authController = new AuthController();
            $userManager->delete($id);
            $authController->logOut();
            //header('Location:/Home/index');
        }
    }
    /**
     * Edit a specific item
     */
    public function edit(int $id): string
    {
        $userManager = new UserManager();
        $reviewsManager = new ReviewManager();
        $user = $userManager->selectOneById($id);
        $rate = $reviewsManager->averageByUser($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = array_map('trim', $_POST);

            if (count($this->checkUserForm()) == 0) {
                if (!isset($user['avatar'])) {
                    $user['avatar'] = $userManager->selectOneById($id)['avatar'];
                } else {
                    $user = $this->uploadAvatar($user);
                }

                $userManager->update($user);
                header('Location: /user/userShow/' . $id);
            } else {
                if (!isset($user['avatar'])) {
                    $user['avatar'] = $userManager->selectOneById($id)['avatar'];
                } else {
                    $user = $this->uploadAvatar($user);
                }
                return $this->twig->render('User/edit.html.twig', [
                    'user' => $user,
                    'errors' => $this->checkUserForm()
                ]);
            }
        }

        $this->restrictLogIn();
        return $this->twig->render('User/edit.html.twig', [
            'user' => $user,
            'rate' => $rate
        ]);
    }

    public function userAdmin()
    {
        $this->restrictAdmin();
        return $this->twig->render('User/userAdmin.html.twig');
    }
}
