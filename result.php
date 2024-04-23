<?php

// includes functions
include "includes/header.php";
include "includes/functions.php";
ini_set("display_errors", 0);
error_reporting(0);

$nb_variable = $_SESSION['nb_variable'];
$nb_contrainte = $_SESSION['nb_contrainte'];
$method = $_SESSION['method']; // 1 max - 2 for min

if (isset($_POST["z_values"], $_POST["c_v_values"], $_POST["contrainte_operations"], $_POST["contrainte_values"])) {
    if (!isset($nb_variable, $nb_contrainte, $method)) {
        header("location: FillContraintes.php");
    }
} else {
    header("location: FillContraintes.php");
}

echo "<div class='container mt-2 mb-5'><a class='btn btn-primary' href='FillContraintes.php' >Retour</a>";

// la case de z
$tab[$nb_contrainte + 1][0] = "Z";

//remplire la premiere ligne du tableau par les X1 X2....
for ($i = 2; $i <= $nb_variable + 1; $i++) {
    $val = $i - 1;
    $tab[0][$i] = "X" . $val;
}

//inserer la les valeurs de contrainte dans le tab
$p = 0;
for ($i = 1; $i <= $nb_contrainte; $i++) {
    for ($j = 2; $j <= $nb_variable + 1; $j++) {
        $tab[$i][$j] = $_POST['c_v_values'][$p];
        $p++;
    }
}

//un compteur sur (inf)
$compt1 = 0;
//un compteur sur (sup)
$compt2 = 0;
//un compteur sur (egal)
$compt3 = 0;

for ($i = 1; $i <= $nb_contrainte; $i++) {
    if ($_POST['contrainte_operations'][$i - 1] == "inf_egal") {
        $compt1++;
    }
    if ($_POST['contrainte_operations'][$i - 1] == "sup_egal") {$compt2++;}

    if ($_POST['contrainte_operations'][$i - 1] == "egal") {$compt3++;}
}

//premiere ligne avec les e et les a
//ajout des var d'écart et var artificielle
$i = 1;
for ($j = 1; $j <= $nb_contrainte; $j++) {
    if ($_POST['contrainte_operations'][$j - 1] == "sup_egal") {
        $tab[0][$i + $nb_variable + 1] = "e" . $j;
        $i = $i + 1;
        $tab[0][$i + $nb_variable + 1] = "a" . $j;
        //$k=$k+1;
    } elseif ($_POST['contrainte_operations'][$j - 1] == "inf_egal") {
        $tab[0][$i + $nb_variable + 1] = "e" . $j;

    } else {
        $tab[0][$i + $nb_variable + 1] = "a" . $j;

    }
    $i = $i + 1;
}

//ajouter B dans la premiere ligne
for ($i = 1; $i <= $nb_contrainte; $i++) //i pour les lignes
{
    if (($_POST['contrainte_operations'][$i - 1] == "inf_egal" && $method = "1") || ($_POST['contrainte_operations'][$i - 1] == "sup_egal" && $method = "2")) {
        $tab[$i][0] = "e" . $i; //premier colonne avec les e en cas d'inf
        for ($k = $nb_variable + 2; $k <= $nb_variable + 1 + $compt1 + 2 * $compt2 + $compt3 + 1; $k++) //k pour les colonnes
        {
            if ($tab[$i][0] == $tab[0][$k]) {
                $tab[$i][$k] = 1;
            } else {
                $tab[$i][$k] = 0;
            }
        }
    } elseif ($_POST['contrainte_operations'][$i - 1] == "egal") {
        $tab[$i][0] = "a" . $i;
        for ($k = $nb_variable + 2; $k <= $nb_variable + 1 + $compt1 + 2 * $compt2 + $compt3 + 1; $k++) {
            if ($tab[$i][0] == $tab[0][$k]) {
                $tab[$i][$k] = 1;
            } else {
                $tab[$i][$k] = 0;
            }
        }
    } else {
        $tab[$i][0] = "a" . $i;
        for ($k = $nb_variable + 2; $k <= $nb_variable + 1 + $compt1 + 2 * $compt2 + $compt3 + 1; $k++) {
            if ($tab[$i][0] == $tab[0][$k]) {
                $tab[$i][$k] = 1;
                $tab[$i][$k - 1] = -1;
            } else {
                $tab[$i][$k] = 0;
            }
        }

    }
}

//remplir le tableau avec la matrice b[]
$k = 0;
for ($i = 1; $i <= $nb_contrainte+1; $i++) {
    $tab[$i][1] = $_POST['contrainte_values'][$k];
    $k++;
}


//remplir la ligne des couts
//cas du simplexe
if ($compt1 == $nb_contrainte) {
    echo "<div class='alert alert-danger mt-3'>Probleme de Simplexe Normal</div> ";
    $tab[$nb_contrainte + 1][0] = "Z";
    $j = 0;
    for ($i = 1; $i <= $nb_variable; $i++) {
        // here is the diff for min its - and for max its +
        if ($method == "1") {
            $tab[$nb_contrainte + 1][$i + 1] = $_POST['z_values'][$j];
        } else {
            $tab[$nb_contrainte + 1][$i + 1] = -($_POST['z_values'][$j]);
        }

        $j++;
    }

    $tab[$compt1+$compt3 + 1][1] = 0; // first time z

    for ($i = 2; $i <= $compt1+$compt3 + 1; $i++) {
        $tab[$nb_contrainte + 1][$i + $nb_variable] = 0;
    }

} else //cas de 2 phases
{
    echo "<div class='alert alert-danger mt-3'>Probleme de Simplexe de 2 Phase</div> ";
    $tab[$nb_contrainte + 1][0] = "Z";

    for ($i = 1; $i <= $nb_variable + $compt1 + 2 * $compt2 + $compt3; $i++) {
        if ($tab[0][$i][0] == 'a') {
            $tab[$nb_contrainte + 1][$i] = -1;
        } else {
            $tab[$nb_contrainte + 1][$i] = 0;
        }
    }

}

//affichage du tableau

affichage($tab, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);

//calcul de Z finale
function Z($tab, $nb_variable, $nb_contrainte, $nbr)
{
    $v = resultatX($tab, $nb_variable, $nb_contrainte, $nbr);
    $Z = 0;
    echo "La Solution est : ";
    for ($i = 1; $i <= $nb_variable; $i++) {
        $Z = $Z + $v[$i] * ($_POST['z_values'][$i - 1]);
        echo "        X" . $i . "=" . $v[$i];

    }
    echo "<br/>";

    echo "Le bénéfice est : Z = " . $Z;

}

function verifierRatio($tab, $nb_variable, $nb_contrainte, $nbr)
{
    $ratio = ratio($tab, $nb_variable, $nb_contrainte, $nbr);
    $p = 0;
    for ($i = 1; $i <= $nb_contrainte; $i++) {
        //$p=0;
        if ($ratio[$i] <= -2) {
            $p++;
        }
    }
    return $p;
}

/////verifier si c un prob de simplexe ou 2 phases
if ($compt1 == $nb_contrainte) {

////////////////////////////////boucle simplexe////////////////////////////
    $p = verifierRatio($tab, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
    if ($p == $nb_contrainte) {
        echo '<h4><p style="color: red;">Problème non borné</p></h4>';
    } else {
        do {
            affichagePivot($tab, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            $tab2 = echelonner($tab, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            affichage($tab2, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            $l = verifier($tab2, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            $tab = $tab2;
            $g = verifierRatio($tab, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            Z($tab, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            if ($g == $nb_contrainte) {
                echo "<br/>";
                echo '<h3><p style="color: red;">Problème non borné</p></h3>';
                break;
            }
        } while ($l == 0);

    }
} else { ////////PHASE1
    echo "<br/><br/>";
    $tabb = echCout($tab, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
    //affichage($tabb,$nb_contrainte,$nb_variable+$compt1+2*$compt2+$compt3);
    $p1 = verifierRatio($tabb, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
    if ($p1 == $nb_contrainte) {
        echo '<h4><p style="color: red;">Problème non borné</p></h4>';
    } else {
        do {
            affichagePivot($tabb, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            $taba = echelonner($tabb, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            affichage($taba, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            $l = verifier($taba, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            $tabb = $taba;
            $Za = Za($tabb, $nb_variable, $nb_contrainte, $compt2 + $compt3, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            echo " Za = " . $Za;
            $g1 = verifierRatio($tabb, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + 2 * $compt2 + $compt3);
            if ($g1 == $nb_contrainte) {
                echo "<br/>";
                echo '<h4><p style="color: red;">Problème non borné</p></h4>';
                break;
            }
        } while ($l == 0);
    }

//Phase 2
    if ($Za == 0) {
        echo "<br/><br/>";
        echo "<center> DEBUT DE LA PHASE 2 </center> ";

        for ($k = 0; $k <= $nb_variable + $compt1 + 2 * $compt2 + $compt3; $k++) {
            if ($tabb[0][$k][0] == 'a') {
                for ($i = 0; $i <= $nb_contrainte + 2; $i++) {
                    for ($j = $k; $j <= $nb_variable + $compt1 + 2 * $compt2 + $compt3; $j++) {
                        $tabb[$i][$j] = $tabb[$i][$j + 1];
                    }
                    array_pop($tabb[$i]);
                }
            }
        }

        echo "<br>";
        //print_r($tabb);
        //ligne du cout du tableau initial de phase2
        $newcout[0] = "Coût";
        for ($i = 1; $i <= $nb_variable; $i++) {
            // here is the diff for min its - and for max its +
            if ($method == "1") {
                $newcout[$i] = $_POST['z_values'][$i - 1];
            } else {
                $newcout[$i] = -($_POST['z_values'][$i - 1]);
            }

        }
        for ($i = $nb_variable + 1; $i <= $nb_variable + $compt1 + $compt2; $i++) {
            $newcout[$i] = 0;
        }
        ///////affiche du tableau initiale de phase deux avec la nouvelle ligne du cout
        $array = $tabb;
        for ($j = 0; $j <= $nb_variable + $compt1 + $compt2; $j++) {
            $array[$nb_contrainte + 1][$j] = $newcout[$j];

        }
        affichage($array, $nb_contrainte, $nb_variable + $compt1 + $compt2);
/////////////Echelonnage de la ligne cout du tableau initiale de la phase 2*$compt2
        $array1 = echNewCout($newcout, $array, $nb_variable, $nb_contrainte, $compt1 + $compt2);
        echo '</br>';
        echo "Nouveau tableau apres modification du cout car ce n'etait pas un vrai tab de simplexe";
        echo '</br>';
        affichage($array1, $nb_contrainte, $nb_variable + $compt1 + $compt2);
        $p2 = verifierRatio($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
        if ($p2 == $nb_contrainte) {
            echo '<h4><p style="color: red;">Problème non borné</p></h4>';
        } else {
            $f = verifier($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
            if ($f == 1) {
                Z($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
            } else {
                //simplexe again
                do {
                    affichagePivot($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
                    $taba = echelonner($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
                    affichage($taba, $nb_contrainte, $nb_variable + $compt1 + $compt2);
                    $l = verifier($taba, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
                    $array1 = $taba;
                    Z($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
                    $g2 = verifierRatio($array1, $nb_variable, $nb_contrainte, $nb_variable + $compt1 + $compt2);
                    if ($g2 == $nb_contrainte) {
                        echo "<br/>";
                        echo '<h3><p style="color: red;">Problème non borné</p></h3>';
                        break;
                    }
                } while ($l == 0);
            }
        }

    } else {
        echo "<br/><br/>";
        echo '<h3><p style="color: red;">Problème non borné</p></h3>';
    }

}

echo "</div>";
