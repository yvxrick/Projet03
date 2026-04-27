<?php
    include_once __DIR__ . '/../app/database/database.php';

    $con = Database::Connect();

    $tri = $_GET['tri'] ?? 'Parution';
    $ordre = $_GET['ordre'] ?? 'ASC';

    $colonnesValides = ['Parution', 'DescCategorie', 'DescriptionAbregee', 'Etat'];

    if(!in_array($tri, $colonnesValides)){
        $tri = 'Parution';
    }

    if($ordre !== 'ASC' && $ordre !== 'DESC'){
        $ordre = 'ASC';
    }

    #Recuperer les annonces de l'utilisateur
    function getAnnonces(){
        global $con;
        global $tri;
        global $ordre;

        $req = "SELECT annonces.*, categories.Description as DescCategorie 
                FROM annonces 
                JOIN categories ON annonces.Categorie = categories.NoCategorie
                WHERE annonces.NoUtilisateur = 3
                ORDER BY $tri $ordre";
    
        $stmt = $con->query($req);
        
        $annonces = $stmt->fetch_all(MYSQLI_ASSOC);
        return $annonces;
    }

    $annonces = getAnnonces();

    $action = $_GET['action'] ?? "ajouterAnnonce";

    function changerEtat($noAnnonce){
        global $con;
        
        $result = $con->query("SELECT Etat FROM annonces WHERE NoAnnonce = $noAnnonce");
        $row = $result->fetch_assoc();
        $etat = $row['Etat'] == 1 ? 2 : 1;
        
        $stmt = $con->prepare("UPDATE annonces SET Etat = ? WHERE NoAnnonce = ?");
        $stmt->bind_param("ii", $etat, $noAnnonce);
        $stmt->execute();
    }
    
    function retraitAnnonce($noAnnonce){
        global $con; 

        $stmt = $con->prepare("UPDATE annonces SET Etat = 3 WHERE NoAnnonce = ?");
        $stmt->bind_param("i", $noAnnonce);
        $stmt->execute();
    }

    function modifierAnnonce($noAnnonce, $categorie, $descAbregee, $descComplete, $prix, $photo){
        global $con;

        $miseAJour = (new DateTime())->format('Y-m-d H:i:s');

        if($photo) {
            $stmt = $con->prepare("UPDATE annonces SET 
                Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, 
                Prix = ?, MiseAJour = ?, Photo = ?
                WHERE NoAnnonce = ?");
            $stmt->bind_param("issdss i", 
                $categorie, 
                $descAbregee, 
                $descComplete, 
                $prix, 
                $miseAJour, 
                $photo, 
                $noAnnonce
            );
        } else {
            $stmt = $con->prepare("UPDATE annonces SET 
                Categorie = ?, DescriptionAbregee = ?, DescriptionComplete = ?, 
                Prix = ?, MiseAJour = ?
                WHERE NoAnnonce = ?");
            $stmt->bind_param("issdsi", 
                $categorie, 
                $descAbregee, 
                $descComplete, 
                $prix, 
                $miseAJour, 
                $noAnnonce
            );
        }
        $stmt->execute();
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {

        global $con;

        $noUtilisateur = 3; 
        $parution = (new DateTime())->format('Y-m-d H:i:s');
        $categorie = $_POST['categorie'];
        $descAbregee = $_POST['descAbregee'];
        $descComplete = $_POST['descComplete'];
        $prix = $_POST['prix'];
        $MiseAJour = null;
        $etat = 1;

        #Sauvegarder la photo
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $nomPhoto = uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../photos-annonce/' . $nomPhoto);
        $photo = 'photos-annonce/' . $nomPhoto;

        #Inserer les donnees
        $stmt = $con->prepare("INSERT INTO annonces (
            NoUtilisateur, 
            Parution, 
            Categorie, 
            DescriptionAbregee, 
            DescriptionComplete, 
            Prix, 
            MiseAJour, 
            Photo, 
            Etat
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");


        $stmt->bind_param("isissdssi", 
            $noUtilisateur, 
            $parution, 
            $categorie, 
            $descAbregee, 
            $descComplete, 
            $prix, 
            $MiseAJour, 
            $photo, 
            $etat
        );
        $stmt->execute();

        echo "<p style='color:green'>Annonce ajoutée avec succès!</p>";
    }

    if($action === 'changerEtat'){
        $noAnnonce = $_GET['noAnnonce'];
        changerEtat($noAnnonce);
        header("Location: gestionAnnonces.php?action=ajouterAnnonce");
        exit();
    }


    if($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'confirmerRetrait'){
        $noAnnonce = $_POST['noAnnonce'];
        retraitAnnonce($noAnnonce);
        header("Location: gestionAnnonces.php?action=ajouterAnnonce");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifierAnnonce'){
        $noAnnonce = $_POST['noAnnonce'];
        $categorie = (int)$_POST['categorie'];
        $descAbregee = $_POST['descAbregee'];
        $descComplete = $_POST['descComplete'];
        $prix = $_POST['prix'];
        
        $photo = null;
        if(!empty($_FILES['photo']['name'])){
            $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $nomPhoto = uniqid() . '.' . $extension;
            move_uploaded_file($_FILES['photo']['tmp_name'], __DIR__ . '/../photos-annonce/' . $nomPhoto);
            $photo = 'photos-annonce/' . $nomPhoto;
        }

        modifierAnnonce($noAnnonce, $categorie, $descAbregee, $descComplete, $prix, $photo);
        header("Location: gestionAnnonces.php?action=ajouterAnnonce");
        exit();
    }

    #
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion annonce</title>
    <link rel="stylesheet" href="profile.css">
</head>   

<body>
    <?php include_once 'menuPrincipal.php' ?>
    
    <div class="contenu">

        <h2>Gestion des annonces</h2>

        <?php if($action === 'ajouterAnnonce'): ?>
            <div class="gestion-annonce">
                <h3>Mes annonces</h3>
                <a href="gestionAnnonces.php?action=afficherAnnonce">+</a>
            </div>
            
        <?php else: ?>
            <div class="gestion-annonce">
                <h3>Ajouter une annonce</h3>
                <a href="gestionAnnonces.php?action=ajouterAnnonce">&#9776;</a>
            </div>
        <?php endif; ?>



    

        <!-- Afficher les annonces de l'utilisateur -->
        <?php if($action === 'ajouterAnnonce'){ ?>

        <?php
            if(empty($annonces)){
                echo "<p>Aucune annonce creer.</p>";
            }
            else{
        ?>
            <!-- Trier les annonces de l'utilisateur-->
            <form method="GET" action="gestionAnnonces.php">
                <input type="hidden" name="action" value="ajouterAnnonce">
                
                <select name="tri">
                    <option value="Parution">Date de parution</option>
                    <option value="DescCategorie">Catégorie</option>
                    <option value="DescriptionAbregee">Description abrégée</option>
                    <option value="Etat">État</option>
                </select>
                
                <select name="ordre">
                    <option value="ASC">Croissant</option>
                    <option value="DESC">Décroissant</option>
                </select>
                
                <button type="submit">Trier</button>
            </form>

            <?php





            ?>

            <div class="listeAnnonce">
                <?php $noSequentiel = 0; ?>
                <?php foreach($annonces as $annonce): ?>
                    <div class="annonce">
                        <p class="noSequentiel"><?= ++$noSequentiel ?></p>
                        <p class="noAnnonce"><?= $annonce['NoAnnonce'] ?>#</p>
                        <p class="dateParution" >Apparu le <?= $annonce['Parution'] ?></p>
                        <p class="categorie"><?= $annonce['DescCategorie'] ?></p>        
                        <img src="../<?= $annonce['Photo'] ?>" alt="">
                        <p><?= $annonce['DescriptionAbregee'] ?></p>
                        <p><?= $annonce['DescriptionComplete'] ?></p>
                        <p><?= $annonce['Prix'] ?? "N/A" ?> $</p>
                        <p><?= $annonce['Etat'] == 1 ? 'Actif' : 'Inactif' ?></p>
                        <div class="wrap-actions">
                            <a class="actions" href="gestionAnnonces.php?action=modifierAnnonce&noAnnonce=<?= $annonce['NoAnnonce'] ?>">Modifier</a>
                            <a class="actions" href="gestionAnnonces.php?action=retraitAnnonce&noAnnonce=<?= $annonce['NoAnnonce']?>">Retrait</a>
                            <a class="actions" href="gestionAnnonces.php?action=changerEtat&noAnnonce=<?= $annonce['NoAnnonce']?>"><?= $annonce['Etat'] == 1 ? 'Désactiver' : 'Activer' ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
                }
            ?>
        <?php
            }
        ?>

<!-- Ajouter une annonce -->
<?php if($action === 'afficherAnnonce'){ ?>


<form method="POST" enctype="multipart/form-data">
            <div>
            <label>Catégorie</label><br>
            <select name="categorie" id="">
                <option value="1">Location</option>
                <option value="2">Recherche</option>
                <option value="3">A vendre</option>
                <option value="4">A donner</option>
                <option value="5">Service offert</option>
                <option value="6">Autre</option>
            </select>

            <div>
                
            <label>Description abrégée</label><br>
            <input type="text" name="descAbregee"></div><br>

            <div><label>Description complète</label><br>
            <textarea name="descComplete" rows="5" cols="40"></textarea></div><br>

            <div><label>Prix</label><br>
            <input type="number" name="prix" step="0.01"></div><br>


            <div id="dropzone" style="border: 2px dashed grey; padding: 40px; text-align:center">
                Glisser une photo ici
                <img id="preview" src="" alt="" style="display:none; max-width:200px; margin-top:10px">
            </div>
            <input type="file" name="photo" id="photo" hidden>

            <script>
                const dropzone = document.getElementById('dropzone');
                const preview = document.getElementById('preview');

                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.style.background = '#f0f0f0';
                });

                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const file = e.dataTransfer.files[0];
                    document.getElementById('photo').files = e.dataTransfer.files;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                });
            </script>

            <button type="submit">Ajouter</button>
        </form>
        <?php
        }
        ?>

        <?php if($action === "modifierAnnonce"): 
            $noAnnonce = $_GET['noAnnonce'];
            $result = $con->query("SELECT * FROM annonces WHERE NoAnnonce = $noAnnonce");
            $a = $result->fetch_assoc();
        ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="noAnnonce" value="<?= $a['NoAnnonce'] ?>">
                
                <select name="categorie">
                    <?php $cats = $con->query("SELECT * FROM categories"); while($cat = $cats->fetch_assoc()): ?>
                        <option value="<?= $cat['NoCategorie'] ?>" <?= $cat['NoCategorie'] == $a['Categorie'] ? 'selected' : '' ?>>
                            <?= $cat['Description'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="text" name="descAbregee" value="<?= $a['DescriptionAbregee'] ?>">
                <textarea name="descComplete"><?= $a['DescriptionComplete'] ?></textarea>
                <input type="number" name="prix" step="0.01" value="<?= $a['Prix'] ?>">

                <button type="submit" name="action" value="modifierAnnonce">Modifier</button>
            </form>
        <?php endif; ?>


        <?php if($action === "retraitAnnonce"): 
            $noAnnonce = $_GET['noAnnonce'];
            $result = $con->query("SELECT annonces.*, categories.Description as DescCategorie 
                                FROM annonces 
                                JOIN categories ON annonces.Categorie = categories.NoCategorie
                                WHERE NoAnnonce = $noAnnonce");
            $a = $result->fetch_assoc();
        ?>
            <div>
                <h3>Confirmer le retrait</h3>
                <p>#<?= $a['NoAnnonce'] ?></p>
                <p><?= $a['DescCategorie'] ?></p>
                <p><?= $a['DescriptionAbregee'] ?></p>
                <p><?= $a['DescriptionComplete'] ?></p>
                <p><?= $a['Prix'] ?> $</p>
                <img src="../<?= $a['Photo'] ?>" style="max-width:200px">

                <form method="POST">
                    <input type="hidden" name="noAnnonce" value="<?= $a['NoAnnonce'] ?>">
                    <input type="hidden" name="action" value="confirmerRetrait">
                    <button type="submit">Confirmer le retrait</button>
                    <a href="gestionAnnonces.php?action=ajouterAnnonce">Annuler</a>
                </form>
            </div>
        <?php endif; ?>

    </div>
    </div>
</body>
</html>