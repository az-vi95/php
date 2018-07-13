<?php
// faire la page qui liste les produits dans un tableau HTML
// tous les champs sauf la description 
// bonus : 
// afficher le nom de la catégorie au lieu de son id 

require_once __DIR__ . '/../include/init.php';
adminSecurity();

// le requêtage ici

$query = <<<SQL
SELECT p.*, c.nom AS categorie_nom
FROM produit p
JOIN categorie c ON p.categorie_id = c.id
SQL;

$stmt = $pdo->query($query);
$produits = $stmt->fetchAll();


require __DIR__ . '/../layout/top.php';
?>

<h1>Gestion produits</h1>

<p><a href="produit-edit.php">Ajouter un produit</a></p>

<!-- le tableau HTML ici -->
<table class="table">
    <tr>
        <th>ID</th>
        <th>NOM</th>
        <th>REFERENCE</th>
        <th>PRIX</th>
        <th>CATEGORIE</th> 
        <th width="350px"></th>
    </tr>
    <?php
    foreach ($produits as $produit) :
    ?>
    <tr>
        <td><?= $produit['id']; ?></td>
        <td><?= $produit['nom']; ?></td>
        <td><?= $produit['reference']; ?></td>
        <td><?= prixFr($produit['prix']); ?></td>
        <td><?= $produit['categorie_nom']; ?></td>
        <td>
            <a class="btn btn-primary"
               href="produit-edit.php?id=<?= $produit['id'];?>">
                Modifier
            </a>
        </td>
          <td>
            <a class="btn btn-danger"
               href="produit-delete.php?id=<?= $produit['id'];?>">
                Supprimer
            </a>
        </td>

    </tr>
    <?php
    endforeach;;
    ?>
</table>   
<?php
require __DIR__ . '/../layout/bottom.php';
?>
