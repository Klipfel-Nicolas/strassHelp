<?php

namespace App\Controller;

use App\Model\AuthManager;

class AuthController extends AbstractController
{
    public function log()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $_POST['mail'];
            $password = $_POST['password'];
            $authManager = new AuthManager();
            $user = $authManager->checkMail($mail);

            if ($_POST['mail']) {
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user']['id'] = $user['id'];
                    $_SESSION['user']['userLastName'] = $user['lastName'];
                    $_SESSION['user']['userFirstName'] = $user['firstName'];
                    $_SESSION['user']['status'] = $user['isAdmin'];
                    $_SESSION['user']['avatar'] = $user['avatar'];
                    $_SESSION['user']['mail'] = $user['mail'];
                    $_SESSION['user']['rang'] = $user['rang'];
                    $_SESSION['user']['badge'] = $user['badge'];

                    header('Location: /');
                } else {
                    return 'Mail ou mot de passe incorrect';
                }
            }
        }
    }

    public function logIn()
    {
        $errors = $this->log();

        return $this->twig->render('Authentification/logIn.html.twig', ['errors' => $errors]);
    }

    public function logOut()
    {
        session_unset();
        session_destroy();
        header('Location: /');
    }
}
