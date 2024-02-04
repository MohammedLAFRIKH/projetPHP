<?php
// Service/EmailService.php

class EmailService {
    public function envoyerCourrielActivation($destinataire, $lienActivation) {
        $sujet = "Activez votre compte";
        $message = "Cliquez sur le lien suivant pour activer votre compte : $lienActivation";

        // Utilisez une bibliothèque de courriel ou la fonction mail() de PHP pour envoyer le courriel
        mail($destinataire, $sujet, $message);
    }
}
