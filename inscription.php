<?php

require_once __DIR__ . '/include/init.php';

$civilite = $nom = $prenom= $email = $ville = $cp = $adresse = '';
$errors = [];
if(!empty($_POST)) {
    sanitizePost();
    extract($_POST);
    if(empty($_POST['civilite'])) {
        $errors[] = 'La civilité est obligatoire';
    }
    if(empty($_POST['nom'])) {
        $errors[] = 'Le nom est obligatoire';
    }
    if(empty($_POST['prenom'])) {
        $errors[] = 'Le prénom est obligatoire';
    }
    if(empty($_POST['email'])) {
        $errors[] = 'L\'email est obligatoire';
    // test de la validité de l'adresse email
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'L\'email n\'est pas valide obligatoire';
    } else {
        $query = 'SELECT count(*) FROM utilisateur WHERE email = :email'; // méthode quote
        $stmt = $pdo->prepare($query);
        $stmt->execute([':email' => $_POST['email']]);
        $nb = $stmt-> fetchColumn();
        
        if($nb != 0) {
            
            $errors[] = 'Cet email est déjà utilisé';
            
            
        }
    }
   
    if(empty($_POST['ville'])) {
        $errors[] = 'La ville est obligatoire';
    }
    if(empty($_POST['cp'])) {
        $errors[] = 'Le code postal est obligatoire';
    // ctype_digit teste qu'une chaine de carcatères ne contient que des chiffres
    } elseif (strlen($_POST['cp']) != 5 || !ctype_digit($_POST['cp'])) {
    $errors[] = "Le code postal n'est pas valide";
        }
    if(empty($_POST['adresse'])) {
        $errors[] = 'L\'adresse est obligatoire';
    }
    if(empty($_POST['mdp'])) {
        $errors[] = 'Le mot de passe est obligatoire';
    }elseif (!preg_match('/^[a-zA-Z0-9_-]{6,20}$/',$_POST['mdp'])) {
        $errors[] = 'Le mot de passe doit faire entre 6 et 20 caractères'
        . ' et ne contenir que des chiffres, des lettre ou les caractères _ '
        . 'et _';
    }
    if($_POST['mdp'] != $_POST['mdp_confirm']) {
        $errors[] = 'Le mot de passe et sa confirmation ne sont pas identiques';
    }
    if(empty($errors)) {
    $query = <<<EOS
INSERT INTO utilisateur (
   civilite, 
   nom,
   prenom,
   email,
   adresse,
   ville,
   cp,
   mdp
)VALUES(
   :civilite, 
   :nom,
   :prenom,
   :email,
   :adresse,
   :ville,
   :cp,
   :mdp
)
   
EOS;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':civilite' =>$_POST['civilite'],
        ':nom' =>$_POST['nom'],
        ':prenom' =>$_POST['prenom'],
        ':email' =>$_POST['email'],
        ':adresse' =>$_POST['adresse'],
        ':ville'=>$_POST['ville'],
        ':cp'=>$_POST['cp'],
        //encryptage du mot de passe à l'enregistrement
        ':mdp' =>password_hash($_POST['mdp'], PASSWORD_BCRYPT),
        
    ]);
    
    setFlashMessage('Votre compte est créé');
    header('Location: index.php');
    die;
    
    }
}     

require __DIR__ . '/layout/top.php';
if (!empty($errors)) : 
?>

<div class="alert alert-danger">
   <h5 class="alert-heading">Le formulaire contient des erreurs</h5>
   <?= implode('<br>', $errors); // transforme un tableau en chaine de caracteres
?>
</div>
<?php
endif;
?>

<h1>Inscription</h1>
<form method="post">
    <div class=form-group">
        <label>Civilité </label>
        <select name="civilite" class="form-control">
            <option value=""></option>
            <option value="Mme" <?php if ($civilite == 'Mme') {echo 'selected';} ?>>Mme</option>
            <option value="M." <?php if ($civilite == 'M.') {echo 'selected';} ?>>M.</option>
        </select>
    </div>
    <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $nom; ?>" >
    </div>
    <div class="form-group">
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-control" value="<?= $prenom; ?>">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" class="form-control" value="<?= $email; ?>">
    </div>
    <div class="form-group">
        <label>Ville</label>
        <input type="text" name="ville" class="form-control" value="<?= $ville; ?>">
    </div>
    <div class="form-group">
        <label>Code postal</label>
        <input type="text" name="cp" class="form-control" value="<?= $cp; ?>">
    </div>
    <div class="form-group">
        <label>Adresse</label>
        <textarea name="adresse" class="form-control"> <?= $adresse; ?> </textarea>
    </div>
    <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="mdp" class="form-control">
    </div>
    <div class="form-group">
        <label>Confirmation du mot de passe</label>
        <input type="password" name="mdp_confirm" class="form-control">
    </div>
    <div class="form-btn-group text-right">
       <button type="submit" class="btn btn-primary">
           Valider
       </button>
   </div>
    
  
</form> 
    

<?php
require __DIR__ . '/layout/bottom.php';
?>