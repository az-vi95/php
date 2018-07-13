<?php
/*
Lister les commandes dans un tableau HTML : 
 * id de la commande
 * nom prénom de l'utilisateur qui a passé la commande
 * montant formaté
 * date de la commande 
 * statut 
 * date du statut
Passer le statut en liste déroulante avec un boutton Modifier
pour changer le statut de la commande en bdd
(nécessite un champ caché pour l'id de la commande )
*/

require_once __DIR__ . '/../include/init.php';
adminSecurity();

if(isset($_POST['modifierStatut'])) {
  $query = <<<SQL
UPDATE commande SET
  statut = :statut,
  date_statut = now()
WHERE id = :id
SQL;
  $stmt = $pdo->prepare($query);
  $stmt->execute([
    ':statut' => $_POST['statut'],
    ':id'     => $_POST['commandeId']
  ]);

  setFlashMessage('Le staut est modifié');
}

?>

<?php

// $query =  'SELECT c.id, u.nom, u.prenom, c.date_commande, c.statut, c.date_statut
// 			FROM commande c
// 			JOIN utilisateur u ON u.id = c.utilisateur_id' ;

// CORRECTION 
$query = <<<SQL
SELECT c.*, concat_ws('', u.prenom, u.nom) AS utilisateur
FROM commande c
JOIN utilisateur u ON c.utilisateur_id= u.id	
SQL;

$stmt = $pdo->query($query);
$commandes = $stmt->fetchAll();

$statuts = [
  'en cours',
  'envoyé',
  'livré',
  'annulé'
];


require __DIR__ . '/../layout/top.php';
?>
<h1>Vos commandes</h1>
<table class="table">
  <tr>
    <th>Id</th>
    <th>Utilisateur</th>
    <th>Montant total</th>
    <th>Date</th>
    <th>Statut</th>
    <th>Date MAJ statut</th>
  </tr>
  <?php
  foreach ($commandes as $commande) :
  ?>
    <tr>
      <td><?= $commande['id']; ?></td>
      <td><?= $commande['utilisateur']; ?></td>
      <td><?= prixFr($commande['montant_total']); ?></td>
      <td><?= datetimeFr($commande['date_commande']); ?></td>
      <td>
        <form method="post" class="form-inline">
          <select name="statut" class="form-control">
            <?php
              foreach ($statuts as $statut) :
                $selected = ($statut == $commande['statut'])
                  ? 'selected'
                  : ''
                ;
            ?>
              <option value="<?= $statut; ?
              >" <?= $selected; ?>>
                <?= $statut; ?>
              </option>
            <?php
          endforeach;
            ?>
          </select>
          <input  type="hidden" 
                  name="commandeId"
                  value="<?= $commande['id']; ?>">
          <button type="submit"
                  class="btn btn-primary"
                  name="modifierStatut">
                    Modifier
          </button>
        </form>          
      </td>
      <td><?= datetimeFr($commande['date_statut']); ?></td>
      
    </tr>
  <?php
  endforeach;
  ?>
</table>



<?php
require __DIR__ . '/../layout/bottom.php';
?>
        