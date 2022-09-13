<?php

require_once('connec.php');

// Si le formulaire est posté
if (!empty($_POST)) {

    $erreurs = array();

    // la fonction trim() retire les espaces en trop avant et après la variable
    if (empty(trim($_POST['pseudo']))) {
        $erreurs[] = 'Merci de saisir votre pseudo';
    }
    if (empty(trim($_POST['message']))) {
        $erreurs[] = 'Vous ne pouvez pas envoyer de message vide';
    }

    if (empty($erreurs)) {

        // nettoyage du html
        $_POST['pseudo'] = htmlspecialchars($_POST['pseudo']);
        $_POST['message'] = htmlspecialchars($_POST['message']);

        // Insertion en base
        $statement = $pdo->prepare("INSERT INTO messages VALUES (NULL,NOW(),:pseudo,:message)");
        $statement->execute(array(
            'pseudo' => $_POST['pseudo'],
            'message' => $_POST['message']
        ));

        // redirection sur soi-même pour éviter de renvoyer le meme formulaire au refresh
        header('location:' . $_SERVER['PHP_SELF']);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace de discussion</title>
    <link rel="stylesheet" href="styleMessage.css">
</head>

<body>
    <header>
        <h1>Espace de discussion</h1>
    </header>
    <main>
        <form method="post">
            <h2>Déposer un message</h2>
            <?php if (!empty($erreurs)) : ?>
                <div class="erreur"><?php echo implode('<br>', $erreurs) ?></div>
            <?php endif; ?>
            <?php if (!empty($infos)) : ?>
                <div class="infos"><?php echo implode('<br>', $infos) ?></div>
            <?php endif; ?>
            <div>
                <input type="text" name="pseudo" placeholder="Votre pseudo" value="<?php echo $_POST['pseudo'] ?? '' ?>">
            </div>
            <div>
                <textarea name="message" rows="4" placeholder="Votre message"><?php echo $_POST['message'] ?? '' ?></textarea>
            </div>
            <div>
                <button type="submit">Envoyer</button>
            </div>
        </form>

        <div class="list_messages">
            <h2>Messages</h2>
            <?php
            // on va chercher les messages triés par date décroissante
            $statement = $pdo->query("SELECT * FROM messages ORDER BY date_message DESC");

            // s'il n'y a pas de lignes
            if ($statement->rowCount() == 0) :
            ?>
                <div class="infos">Pas encore de messages</div>
                <?php
            // sinon
            else :
                // on décharge le tout dans une variable
                $messages = $statement->fetchAll();
                // je boucle sur les messages
                foreach ($messages as $message) :
                ?>
                    <div class="message">
                        <p>
                            <?php
                            // pour restituer les sauts de ligne, je les remplace par br à l'affichage 
                            echo str_replace(PHP_EOL, '<br>', $message['message']);
                            ?>
                        </p>
                        <hr>
                        <div class="align-end">
                            <span>Déposé par <strong><?php echo $message['pseudo'] ?></strong> le <?php echo date('d/m/Y à H:i', strtotime($message['date_message'])) ?></span>
                        </div>
                    </div>
            <?php
                endforeach;
            endif;

            ?>
        </div>
    </main>
</body>

</html>