<?php

namespace App\Controller;

use DateTime;
use App\Model\AuthManager;

class CheckForm extends AbstractController
{
    public function checkEmptyErrors()
    {
        $errors = array();

        foreach ($_POST as $key => $value) {
            if (($value == '' || empty($key)) && ( $key !== 'phoneNumber' && $key !== 'id')) {
                $errors[$key] = $key . "1";
            }
        }

        return $errors;
    }

    public function displayEmptyErrors()
    {
        $errorsMessages = [
            'lastName1' => " - Vous devez remplir votre Nom",
            'firstName1' => " - Vous devez entrer votre prénom",
            'age1' => " - Veuillez renseigner votre date de naissance",
            'adresseNumber1' => " - Veuillez rentrer une adresse valide",
            'adresseStreet1' => " - Veuillez rentrer une adresse valide",
            'adressePostal1' => " - Veuillez rentrer une adresse valide",
            'adresseCity1' => " - Veuillez rentrer une adresse valide",
            'mail1' => " - Veuillez saisir une adresse mail",
            'password1' => " - Entrez un mot de passe",
            'rate1' => "La note doit être d'une étoile minimum",
            'comment1' => "Laisez un commentaire pour votre helper",
            'title1' => 'Nous avons besoin d\'un titre pour votre annonce !',
            'category1' => 'Veuillez choisir une catégorie.',
            'description1' => 'Décrivez en quelque mots votre annonce s\'il vous plait. ',
            'disponibility_id1' => 'Veuillez définir vos disponibilitées.',
            'category_id1' => 'Veuillez selectionner une catégorie',
            'message1' => 'merci de rajouter des informations supplémentaire a votre message.'
        ];

        $error = $this->checkEmptyErrors();
        $adresseError = ['adresseNumber1', 'adresseStreet1', 'adressePostal1', 'adresseCity1'];

        if (count($error) > 0) {
            $errors = array();
            foreach ($errorsMessages as $key => $value) {
                if (in_array($key, $error) && !in_array($key, $adresseError)) {
                    $errors[$key] = $value;
                } elseif (in_array($key, $error) && in_array($key, $adresseError)) {
                    $errors['adress'] = " - Veuillez saisir une adresse valide";
                }
            }
            return $errors;
        }
    }

    public function displayAgeErrors(): array
    {
        $errors = array();
        if ($_POST['age']) {
            $age = new DateTime($_POST['age']);
            $now = new DateTime(date("Y-m-d"));

            if ($now->diff($age)->y < 18) {
                $errors['age2'] = " - Vous devez être majeur" ;
            }
        }
        return $errors;
    }

    public function displayMailPasswodErrors()
    {
        $errors = array();
        $authManager = new AuthManager();

        if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
            $errors['email2'] = " - mauvais format de l'adresse mail";
        }

        if ($authManager->checkMail($_POST['mail']) && !isset($_SESSION['user'])) {
            $errors['mail3'] = " - email déjà utilisé";
        }

        if (strlen($_POST['password']) < 6) {
            $errors['password2'] = " - Le mots de passe doit contenir 6 caractère au moins";
        }

        return $errors;
    }

    public function displayAvatarErrors()
    {
        $errors = array();

        if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
            $tailleMax = 2097152;
            $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
            $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));

            if ($_FILES['avatar']['size'] >= $tailleMax) {
                $errors['avatar2'] = "Taille de l'image trop grande";
            }
            if (!in_array($extensionUpload, $extensionsValides)) {
                $errors['avatar1'] = "Format autorisé 'jpg', 'jpeg', 'gif', 'png' sont autoriser";
            }
        } elseif ((!isset($_FILES['avatar']) || $_FILES['avatar']['size'] == 0) && !isset($_SESSION['user'])) {
            $errors['avatar3'] = "Photo obligatoire";
        }

        return $errors;
    }
}
