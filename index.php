<?php 
    // connect to bdd localhost and bdd online_forma_pro
    $user = 'online_f';
    $pass = "@Y(gF5lOTdVBkyH4";
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=online_forma_pro', $user, $pass);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bdd->exec("SET NAMES 'utf8';");
        // echo "Connexion réussie";
    } catch(Exception $e) {
            echo 'Erreur : '.$e->getMessage();
    }
    // print_r($_POST);
    $tasklist = $bdd->prepare("SELECT id_task as id,titre,status,description,date_in,date_end FROM tasklist");
    $update_status = $bdd->prepare("UPDATE tasklist SET status = ? WHERE id_task = ?");
    $update_all = $bdd->prepare("UPDATE tasklist SET titre = ?, description = ?, status = ?, date_end = ? WHERE id_task =?");
    $delete_task = $bdd->prepare("DELETE FROM tasklist WHERE id_task =?");
    $add_task = $bdd->prepare("INSERT INTO tasklist (titre,description,status,date_in,date_end) VALUES (?,?,?,NOW(),?)");
    if(isset($_POST['action'])){
        switch($_POST['action']){
            case 'add':
                $titre = $_POST['title'];
                $description = $_POST['description'];
                $date_end = $_POST['date_end'] != "" ? $_POST['date_end'] : null;
                $statut = $_POST['statut'];
                $add_task->execute(array($titre,$description,$statut,$date_end));
                break;
            case 'supprimer':
                $id_task = $_POST['id'];
                $delete_task->execute(array($id_task));
                break;
            case 'update':
                $id_task = $_POST['id'];
                $date_end = $_POST['date_end'] != "" ? $_POST['date_end'] : null;
                $update_all->execute(array($_POST['title'],$_POST['description'],$_POST['statut'],$date_end,$id_task));
                break;
            default:
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>taskmanager V2</title>
</head>
<body>
    <h1>taskmanager V2</h1>
    <h2>Liste des tâches</h2>
    <div class="tasklist">
        <?php $tasklist->execute();
        foreach($tasklist as $task){ 
            $form = false;
            if(isset($_POST['action'])){ 
                if($_POST['action'] == "modifier" && $_POST['id'] == $task['id']){
                    $form = true;
                }
            }
            if($form == true){?>
                <form method="post" class="task">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $task['id'];?>">
                    <input type="text" name="title" value="<?php echo $task['titre'];?>">
                    <input type="text" name="description" value="<?php echo $task['description'];?>">
                    <input type="datetime-local" name="date_end" value="<?php echo $task['date_end'];?>">
                    <select name="statut">
                        <option value="A faire" <?php if($task['status']=="A faire"){ echo "selected";} ?>>A faire</option>
                        <option value="En Cour" <?php if($task['status']=="En Cour"){ echo "selected";} ?>>En cour</option>
                        <option value="En Attente" <?php if($task['status']=="En Attente"){ echo "selected";} ?>>En attente</option>
                        <option value="Terminé" <?php if($task['status']=="Terminé"){ echo "selected";} ?>>Terminé</option>
                    </select>
                    <input type="submit" value="update">
                </form>
            <?php }else{?>
                <div class="task">
                    <h3><?php echo $task['titre'];?></h3>                
                    <p class="status">Status : <?php echo $task['status'];?></p>
                    <p class="dateL"><?php echo $task['date_end'];?></p>
                    <p class="desc"><?php echo $task['description'];?></p>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $task['id'];?>">
                        <input type="submit" name="action" value="modifier">
                        <input type="submit" name="action" value="supprimer">
                    </form>
                    <p class="dateI"><?php echo $task['date_in'];?></p>
                </div>
            <?php } }?>
    </div>
    <h2>Ajouter une tâche</h2>
    <form method="post">
        <label for="title">Titre</label>
        <input type="text" name="title" id="title">
        <label for="description">Description</label>
        <input type="text" name="description" id="description">
        <label for="statut">Statut</label>
        <select name="statut" id="statut">
            <option value="A faire">A faire</option>
            <option value="En Cour">En cour</option>
            <option value="En Attente">En attente</option>
            <option value="Terminé">Terminé</option>
        </select>
        <label for="date_end">Date de fin</label>
        <input type="datetime-local" name="date_end" id="date_end">
        <input type="submit" value="Ajouter">
        <input type="reset" value="Effacer">
        <input type="hidden" name="action" value="add">
    </form>

</body>
</html>