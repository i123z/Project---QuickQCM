<?php
session_start(); // Démarrer la session

$matieresProfesseur = []; // Initialisation d'un tableau pour stocker les matières du professeur

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['email']) && isset($_SESSION['mdp'])) {
    try {
        // Connectez-vous à la base de données
        $bdd = new PDO("mysql:host=localhost:3308;dbname=projetqcm;charset=utf8mb4", 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer l'email de l'utilisateur connecté
        $email = $_SESSION['email'];

        // Préparer la requête pour récupérer les informations de l'utilisateur
        $recupUser = $bdd->prepare('SELECT * FROM professeurs WHERE email = ?');
        $recupUser->execute(array($email));
        $userData = $recupUser->fetch(PDO::FETCH_ASSOC);

        // Vérifier si les données utilisateur ont été récupérées
        if ($userData) {
            $id_prof = $userData['id'];

            // Préparer la requête pour récupérer les matières du professeur
            // Préparer la requête pour récupérer les matières du professeur
            $recupMatiere = $bdd->prepare('SELECT DISTINCT matiere.nom_matiere 
                                FROM professeurs 
                                INNER JOIN matiere ON professeurs.id = matiere.id_prof
                                WHERE professeurs.id = ?');
            $recupMatiere->execute(array($id_prof));
            $matiereData = $recupMatiere->fetchAll(PDO::FETCH_ASSOC);


            // Stocker les matières dans un tableau
            foreach ($matiereData as $matiere) {
                $matieresProfesseur[] = $matiere['nom_matiere'];
            }
        } else {
            // L'utilisateur n'existe pas dans la base de données, déconnecter l'utilisateur
            session_destroy();
            header('Location: login.php'); // Rediriger vers la page de connexion
            exit;
        }
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} else {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header('Location: login.php');
    exit;
}

// Utilisez $matieresProfesseur comme vous le souhaitez dans votre code
?>








<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Creation Qcm</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel="stylesheet" href="./stylecreation.css">
  <link rel="icon" href="images/logo2.png" type="image/png">
  
  
</head>
<body>

<div class="demo-page">
  <div class="demo-page-navigation">
    <nav><br><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
      <img src="images/logo.png" alt="Logo du site" class="logo"><br><br>
      <ul>
        <li><a href="Acc.php"><svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19c0 .6.4 1 1 1h3v-3c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v3h3c.6 0 1-.4 1-1v-8.5"/>
        </svg>Acceuil
        </a></li><hr>
        <li>
          <a href="#Identification">
            <svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-width="2" d="M7 17v1c0 .6.4 1 1 1h8c.6 0 1-.4 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>

            Identification</a>
        </li>
        <li>
          <a href="#Spécifications">
            <svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.6-8.5h0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
              
            Spécifications</a>
        </li>
        <li>
          <a href="#Instructions">
            <svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.6 8.5h8m-8 3.5H12m7.1-7H5c-.2 0-.5 0-.6.3-.2.1-.3.3-.3.6V15c0 .3 0 .5.3.6.1.2.4.3.6.3h4l3 4 3-4h4.1c.2 0 .5 0 .6-.3.2-.1.3-.3.3-.6V6c0-.3 0-.5-.3-.6a.9.9 0 0 0-.6-.3Z"/>
            </svg>
            Instructions</a>
        </li>
        <li>
          <a href="#Questions">
            <svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z"/>
            </svg>
            Questions</a>
        </li>
        <li>
            <a href="#Apercu">
              <svg class="w-[24px] h-[24px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3c.6 0 1 .4 1 1v15c0 .6-.4 1-1 1H6a1 1 0 0 1-1-1V5c0-.6.4-1 1-1h3m0 3h6m-6 7 2 2 4-4m-5-9v4h4V3h-4Z"/>
              </svg>
              Aperçu</a>
          </li>
      </ul>
    </nav>
  </div>
  <main class="demo-page-content">
    <section class="intro1">
      <div class="href-target" id="intro"></div>
      <h1 class="package-name"><strong id="s3">Création de QCM</strong></h1>
      <h2><strong id="s2">Remplissez les parties de ce formulaire pour créer un nouveau QCM :</strong> 
      </h2><br>
      <ul>
        <li>Saisissez les informations générales sur le QCM.</li>
        <li>Ajoutez les questions et les composants nécessaires. </li>
      </ul><p>Assurez-vous de fournir des détails précis pour chaque section afin de garantir la qualité du QCM.</p>
      <a  id="Identification"></a>
      <strong id="s1">NB:</strong>
      <p>Vous pouvez revenir à tout moment pour modifier les informations en utilisant les ancres situées à gauche.</p>
      
    </section>
    <section>
      
      <h1>
        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-width="2" d="M7 17v1c0 .6.4 1 1 1h8c.6 0 1-.4 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>              
        Identification
      </h1>
      
      <form id="qcmForm" class="nice-form-group" method="post"> <!-- Ajoutez l'action et la méthode POST -->

        <label>École :</label>
        <input type="text" name="ecole" disabled placeholder="Junia Maroc" value="Junia Maroc" />
      
        <label><br><br>Nom du Professeur :</label>
        <input type="text" name="professeur"disabled placeholder="<?php echo $userData['Nom']; ?>" value="<?php echo $userData['Nom']; ?>" >
        <label><br><br>Niveau :</label>
        <select name="niveau">
          <option>Choisissez un niveau</option>
          <option>JM1</option>
          <option>JM2</option>
          <option>JM3</option>
          <option>JM Soutenance</option>
        </select>
      
        <label><br><br>Matière :</label>
        <select name="matiere">
        <option>Choisissez une matière</option>
        <?php 
        foreach ($matieresProfesseur as $matiere) {
            echo "<option>$matiere</option>";
        }
        ?>
    </select>
        <label><br></label>
        
        
        <a id="Spécifications"></a>
        <label><br></label><label><br><hr></label><label><br></label>
    

    
    
      <h1>
        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.6-8.5h0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        Spécifications
      </h1>
        
      <label><br></label>
        <label>Type du QCM :</label>
        <small>Préciser le type du QCM, <strong>ex:</strong> concours, examen final, contrôle, exercice .....etc</small>
        <input type="text" name="type_qcm" placeholder="Type" />
      
        <label><br><br>Date du QCM :</label>
        <input type="date" name="date_qcm" value="jj-mm-aaaa" />
      
        <label><br><br>Durée (en heures) :</label>
        <input type="number" name="duree_qcm" placeholder="1,30" />
        
        <a id="Instructions"></a>
        <label><br></label><label><br><hr></label><label><br></label>
     
    
    
  
      <h1>
        <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.6 8.5h8m-8 3.5H12m7.1-7H5c-.2 0-.5 0-.6.3-.2.1-.3.3-.3.6V15c0 .3 0 .5.3.6.1.2.4.3.6.3h4l3 4 3-4h4.1c.2 0 .5 0 .6-.3.2-.1.3-.3.3-.6V6c0-.3 0-.5-.3-.6a.9.9 0 0 0-.6-.3Z"/>
          </svg>
          Instructions
      </h1>
      
      <label><br></label>
        <label>Calculatrice autorisée ?</label>
        <input type="radio" name="calculatrice_autorisee" id="r-1" value="oui" />
        <label for="r-1">Oui</label>
      
        <input type="radio" name="calculatrice_autorisee" id="r-2" value="non" />
        <label for="r-2">Non</label>



        <label><br><br>Documents autorisés :</label>
        <input type="radio" name="documents_autorises" id="r-1" value="oui" />
        <label for="r-1">Oui</label>
      
        <input type="radio" name="documents_autorises" id="r-2" value="non" />
        <label for="r-2">Non</label>
      
        
      
        <label><br><br>Autres Consignes :</label>
        <textarea rows="3" name="autres_consignes"></textarea>
      
        <label><br><br>Points pour une réponse correcte :</label>
        <input type="number" name="points_correcte" placeholder="2" />

    
        <label><br><br>Points pour une réponse incorrecte :</label>
        <input type="number" name="points_incorrecte" placeholder="-1" />
    
    
        <a id="Questions"></a>
        <label><br></label><label><br><hr></label><label><br></label>
      
        <h1>
            <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z"/>
            </svg>
            Questions
        </h1>
    
        <div id="questionsContainer">
          <div class="question nice-form-group" data-initial="true">
            <label>Question 1 :</label>
            <input type="text" name="question[]" placeholder="Question" />
           
            <label><br><br>Réponse A :</label>
            <input type="text" name="reponse_A[]" placeholder="Réponse A" />
            <label><br><br>Réponse B :</label>
            <input type="text" name="reponse_B[]" placeholder="Réponse B" />
            <label><br><br>Réponse C :</label>
            <input type="text" name="reponse_C[]" placeholder="Réponse C" />
            <label><br><br>Réponse D :</label>
            <input type="text" name="reponse_D[]" placeholder="Réponse D" />
            <label><br><br>Réponse Correcte :</label>
            <select name="reponse_correcte[]">
              <option>A</option>
              <option>B</option>
              <option>C</option>
              <option>D</option>
            </select>
            <label><br></label>

          </div>
        </div>
        <label><br></label>
      
        <button type="button" class="ajouterQuestion">Ajouter une question</button>
        <button type="submit" class="validerButton">Valider</button>

        <script>
       document.querySelector('.ajouterQuestion').addEventListener('click', function() {
    dupliquerQuestion();
});

function dupliquerQuestion() {
    const questionsContainer = document.getElementById('questionsContainer');
    const questionInitiale = questionsContainer.querySelector('.question[data-initial="true"]');
    const toutesLesQuestions = questionsContainer.querySelectorAll('.question');
    const dernierNumeroQuestion = toutesLesQuestions.length + 1;
    const nouvelleQuestion = questionInitiale.cloneNode(true);

    // Réinitialisation des valeurs des champs de formulaire dans la nouvelle question
    nouvelleQuestion.querySelectorAll('input, select, textarea').forEach(function(element) {
        element.value = ''; // Réinitialise la valeur à une chaîne vide
    });

    nouvelleQuestion.querySelector('label').textContent = `Question ${dernierNumeroQuestion} :`;
    questionsContainer.appendChild(nouvelleQuestion);
     // Ajout d'une ligne <hr> après l'ajout de la nouvelle question
    const hrElement = document.createElement('hr');
    questionsContainer.appendChild(hrElement);
    questionsContainer.appendChild(nouvelleQuestion);
            // Ajout du bouton de suppression uniquement pour les questions ajoutées
            const supprimerButton = document.createElement('button');
            supprimerButton.textContent = 'Supprimer  question';
            supprimerButton.type = 'button';
            supprimerButton.classList.add('supprimerQuestion');
            nouvelleQuestion.appendChild(supprimerButton);
            supprimerButton.addEventListener('click', function() {
              supprimerQuestion(this);
              updateQuestionNumbers();
            });
            updateQuestionNumbers();
  
          }

        
function supprimerQuestion(button) {
    const question = button.parentNode; // Accéder à la div question parente du bouton
    const hrElement = question.nextElementSibling; // Récupérer l'élément <hr> suivant la question
    question.parentNode.removeChild(question); // Supprimer la question
    // Rechercher toutes les lignes <hr> dans le conteneur des questions
    const hrElements = questionsContainer.querySelectorAll('hr');
    // Supprimer la première ligne <hr> trouvée
    if (hrElements.length > 0) {
      hrElements[0].parentNode.removeChild(hrElements[0]);
    }

    updateQuestionNumbers();
}
function updateQuestionNumbers() {
    const questionsContainer = document.getElementById('questionsContainer');
    const questions = questionsContainer.querySelectorAll('.question');

    questions.forEach(function(question, index) {
        question.querySelector('label').textContent = `Question ${index + 1} :`;
    });
}
        </script>
        
        
      </form>
   
    </section>
    
    <section>
      <div class="href-target" id="Apercu"></div>
      <h1>
          <svg class="w-[48px] h-[48px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3c.6 0 1 .4 1 1v15c0 .6-.4 1-1 1H6a1 1 0 0 1-1-1V5c0-.6.4-1 1-1h3m0 3h6m-6 7 2 2 4-4m-5-9v4h4V3h-4Z"/>
          </svg>
          Aperçu
      </h1>
      <form id="dataForm" method="post" action="traitement.php" style="display: none;">
          <!-- Champs pour les données principales -->
          <input type="hidden" name="ecole">
          <input type="hidden" name="professeur">
          <input type="hidden" name="niveau">
          <input type="hidden" name="matiere">
          <input type="hidden" name="type_qcm">
          <input type="hidden" name="date_qcm">
          <input type="hidden" name="duree_qcm">
          <input type="hidden" name="calculatrice_autorisee">
          <input type="hidden" name="documents_autorises">
          <input type="hidden" name="autres_consignes">
          <input type="hidden" name="points_correcte">
          <input type="hidden" name="points_aucune_reponse">
          <input type="hidden" name="points_incorrecte">
          <!-- Champ pour les questions -->
          <input type="hidden" name="questions" id="questions">
      </form>
      <div id="contentContainer">Ici s'affichera l'Aperçu du QCM, après la validation des données du formulaire de Création :</div>
      <label><br></label>
      <button id="btnGenererPDF" type="submit">Créer QCM</button>
      
            <script>
                // Sélection du formulaire
                const form = document.getElementById('qcmForm');
                // Sélection du conteneur de l'aperçu
                const apercuContainer = document.getElementById('contentContainer');
              
                // Gestion de l'événement de soumission du formulaire
                form.addEventListener('submit', function(event) {
                  // Empêcher le comportement par défaut du formulaire qui recharge la page
                  event.preventDefault();
            
                // Récupérer les valeurs des champs du formulaire
                const ecole = form.querySelector('input[name="ecole"]').value;
                const professeur = form.querySelector('input[name="professeur"]').value;
                const niveau = form.querySelector('select[name="niveau"]').value;
                const matiere = form.querySelector('select[name="matiere"]').value;
                const type_qcm = form.querySelector('input[name="type_qcm"]').value;
                const date_qcm = form.querySelector('input[name="date_qcm"]').value;
                const duree_qcm = form.querySelector('input[name="duree_qcm"]').value;
                const calculatrice_autorisee = form.querySelector('input[name="calculatrice_autorisee"]:checked').value;
                const documents_autorises = form.querySelector('input[name="documents_autorises"]:checked').value;
                const autres_consignes = form.querySelector('textarea[name="autres_consignes"]').value;
                const points_correcte = form.querySelector('input[name="points_correcte"]').value;
                const points_incorrecte = form.querySelector('input[name="points_incorrecte"]').value;
            
                // Récupérer les questions et les réponses
                const questions = [];
                const questionsElements = form.querySelectorAll('.question');
                questionsElements.forEach(questionElement => {
                  const question = {
                    enonce: questionElement.querySelector('input[name="question[]"]').value,
                    reponse_A: questionElement.querySelector('input[name="reponse_A[]"]').value,
                    reponse_B: questionElement.querySelector('input[name="reponse_B[]"]').value,
                    reponse_C: questionElement.querySelector('input[name="reponse_C[]"]').value,
                    reponse_D: questionElement.querySelector('input[name="reponse_D[]"]').value,
                    reponse_correcte: questionElement.querySelector('select[name="reponse_correcte[]"]').value
                  };
                  questions.push(question);
                });

                // Remplir les champs cachés du formulaire invisible
                const dataForm = document.getElementById('dataForm');
                dataForm.querySelector('input[name="ecole"]').value = ecole;
                dataForm.querySelector('input[name="professeur"]').value = professeur;
                dataForm.querySelector('input[name="niveau"]').value = niveau;
                dataForm.querySelector('input[name="matiere"]').value = matiere;
                dataForm.querySelector('input[name="type_qcm"]').value = type_qcm;
                dataForm.querySelector('input[name="date_qcm"]').value = date_qcm;
                dataForm.querySelector('input[name="duree_qcm"]').value = duree_qcm;
                dataForm.querySelector('input[name="calculatrice_autorisee"]').value = calculatrice_autorisee;
                dataForm.querySelector('input[name="documents_autorises"]').value = documents_autorises;
                dataForm.querySelector('input[name="autres_consignes"]').value = autres_consignes;
                dataForm.querySelector('input[name="points_correcte"]').value = points_correcte;
                dataForm.querySelector('input[name="points_incorrecte"]').value = points_incorrecte;
                dataForm.querySelector('input[name="questions"]').value = JSON.stringify(questions);
            
                // Construction de la chaîne HTML pour afficher l'aperçu
                let apercuHtml = `<br><u><h1 id="countainertr"><strong id="colortr">Type du QCM:</strong> ${type_qcm}</h1></u><br><br>
                <table>
                <tr>
                <th>École:</th>
                <th>Nom du Professeur:</th>
                <th>Niveau:</th>
                <th>Matière:</th>
                </tr>
        
                <tbody>
                <tr>
                <td>${ecole}</td>
                <td>${professeur}</td>
                <td>${niveau}</td>
                <td>${matiere}</td>
                </tr>
                </tbody>


                <tr>
                <th>Date du QCM:</th>
                <th>Durée (en heures):</th>
                <th>Calculatrice autorisée:</th>
                <th>Documents autorisés:</th>
                </tr>

                <tbody>
                <tr>
                <td>${date_qcm}</td>
                <td>${duree_qcm}</td>
                <td>${calculatrice_autorisee}</td>
                <td>${documents_autorises}</td>
                </tr>
                </tbody>

                <tr>
                <th colspan=2>Autres Consignes:</th>
                <th>Points pour une réponse correcte:</th>
                <th>Points pour une réponse incorrecte:</th>
                </tr>

                <tbody>
                <tr>
                <td colspan=2>${autres_consignes}</td>
                <td>${points_correcte}</td>
                <td>${points_incorrecte}</td>
                </tr>
                </tbody>

                </table>
                  
              
                  <br><br><u><h1><strong>Questions:</strong></h1></u>
                `;
            
                // Ajout des questions et réponses dans l'aperçu
                questions.forEach((question, index) => {
                  apercuHtml += `
                    <div>
                      <ul><strong id="colortr2">Question ${index + 1}:</strong> ${question.enonce}</ul>
                      <li><strong >Réponse A:</strong> ${question.reponse_A}</li>
                      <li><strong >Réponse B:</strong> ${question.reponse_B}</li>
                      <li><strong >Réponse C:</strong>  ${question.reponse_C}</li>
                      <li><strong >Réponse D:</strong>  ${question.reponse_D}</li>
                      <p><strong id="colorrpco" >Réponse correcte:</strong>  ${question.reponse_correcte}</p><hr>
                    </div>
                  `;
                });
            
                // Affichage de l'aperçu dans le conteneur de l'aperçu
                apercuContainer.innerHTML = apercuHtml;
                

  
                document.getElementById('btnGenererPDF').addEventListener('click', function() {
                // Soumettre le formulaire invisible lorsque le bouton "Créer QCM" est cliqué
                document.getElementById('dataForm').submit();
                 });

                 });
                </script>
            
          </section>

        <footer>© 2024 Quick qcm - Tous droits réservés</footer>
      </main>
      </div>
      

    


  
</body>
</html>
