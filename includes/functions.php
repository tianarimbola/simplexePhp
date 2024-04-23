<?php

/////////////////////////////////récuperer l'emplacement du max dans une ligne /////////////////////////////////////
function emplacementMax($ligne)
{
	$k=$ligne[1];
	for($i=1;$i<=count($ligne);$i++)
	{
		if(($ligne[$i])>=$k)
		{$k=$ligne[$i];
	      $empl=$i;}
	 
	}
	
	return $empl;
}



/////////////////////////////////récuperer une ligne du tableau commençant par l'indice 1////////////////////////////////
function ligne($tab,$array_length,$l)//nbr est le nombre des elements de la ligne....$l est la ligne qu'on veut recuperer
{
	for($i=1;$i<=$array_length;$i++)
	{
		$lin[$i]=$tab[$l][$i];
	}
	return $lin;
}

//récuperer un colonne du tableau commençant par l'indice 1
function colonne($tab,$nb_contrainte,$col) //$nb_contrainte le nombre des contraintes (element du colonne) 
{
    for($i=1;$i<=$nb_contrainte;$i++)
	{
		$colonne[$i]=$tab[$i][$col];
	}
	return $colonne;	
}

/*
//fonction qui retourne la variable entrante
function entrante($tab,$array_length,$nb_contrainte)// $nb_contrainte indice sur la ligne (derniere ligne) et $array_length est le nombre des elements de la ligne ...dans le tab $nb_contrainte est le nombre des contraintes
{   
    $l0=ligne($tab,$array_length,$nb_contrainte);// $nb_contrainte doit etre la derniere ligne du tableau
	$i=emplacementMax($l0);//recupere l'indice du max dans la derniere ligne 
	$var=$tab[0][$i];//$var retourne la valeur avec l'indice du max colonne
	return $var;
}
*/



//calcul du ratio
function ratio($tab,$nb_variable,$nb_contrainte,$array_length)// $nb_variable -> nbr des var    $nb_contrainte -> nbr des contraintes    $array_length -> $nb_variable+$compt1+2*$c.....
{   
	//recuperer l'indice
	$line=ligne($tab,$array_length,$nb_contrainte+1);
	$indice=emplacementMax($line);
	//la colonne de la var entrante 
	$column=colonne($tab,$nb_contrainte,$indice);
	//la colonne B
	$B=colonne($tab,$nb_contrainte,1);

	for($i=1;$i<=$nb_contrainte;$i++)
	{
		if($column[$i]>0)
		{
		  $ratio[$i]=($B[$i])/($column[$i]);	
		}
		else
		{
			$ratio[$i]=-2;
		}
		
	}

	return $ratio;
}

//récuperer l'emplacement du min dans une ligne
//cette fonction nous donne l'emplacement de la variable sortante
function vallMin($col)
{    
    $i=1;
	$k=$col[$i];
	while($col[$i]==-2)
	{$k=$col[$i+1];
	$i++;}
	for($j=$i+1;$j<=count($col);$j++)
	{ 
        //$e=0;
		if($col[$j]!=-2)
		{
			if($k>=$col[$j])
			{
				$k=$col[$j];
			}
			
		}
	}
	return $k;	
}
function emplacementMin($col)
{
	$k=vallMin($col);
	for($j=1;$j<=count($col);$j++)
	{ 
        //$e=0;
		if($col[$j]==$k)
		{
			$emplacement=$j;
			
		}
	}
	return $emplacement;
}


//Affiche le tableau avec le ratio et le pivot en rouge
function affichagePivot($tab1,$nb_variable,$nb_contrainte,$array_length)
{
	//le nouveau tableau transformé
	$cout=ligne($tab1,$array_length,$nb_contrainte+1);
	$emplMax=emplacementMax($cout);
	$ratio=ratio($tab1,$nb_variable,$nb_contrainte,$array_length);
	$emplMin=emplacementMin($ratio);
	
	$tab2=$tab1;
	//affichage

     echo'<table class="table mt-5 table-bordered">
     	<tr>
     		<td>B</td><td>b</td>';

   //premiere ligne les x1 .. e1 ...a1 ...et B
  for($i=2;$i<=$array_length+1;$i++)
  {
    echo"<th>";
    echo $tab2[0][$i];
    echo "</th>";
  }
 
 echo"</tr>";

  //premier colonne
for($i=1;$i<=$nb_contrainte+1;$i++)
{
	echo"<tr><th>";
	echo $tab2[$i][0];  
	echo"</th>";
	for($j=1;$j<=$array_length+1;$j++)  
	{   
		if($i==$emplMin && $j==$emplMax)
		{
			echo '<td  class="pivot">' , round($tab2[$i][$j],2) , '</td>';	
		}else{
			echo '<td>',round($tab2[$i][$j],2),"</td>";
		}
	} 
	echo"</tr>";
}
echo "</table>";
}

////////////////////////Affichage du tableau simple
function affichage($tab,$nb_contrainte,$array_length)
{
	
echo'<table class="mt-5 table table-bordered">
  <tr>
     <th>B</th><th>b</th>';

   //premiere ligne les x1 .. e1 ...a1 ...et B
  for($i=2;$i<=$array_length+1;$i++)
  {
    echo"<th>";
    echo $tab[0][$i];
    echo "</th>";
  }
 echo"</tr>";

  //premier colonne
for($i=1;$i<=$nb_contrainte+1;$i++)
{
	echo"<tr>
	     <th>";
	echo $tab[$i][0];  
	echo"</th>";
	for($j=1;$j<=$array_length+1;$j++)  
	{
		echo"<td>";
		echo round($tab[$i][$j],1);
		echo"</td>";
	} 
	echo"</tr>";
}
echo "</table>";

}
////////////////////////fonction que verifie la ligne du cout 
function verifier($tab,$nb_variable,$nb_contrainte,$array_length)
{
	$cout=ligne($tab,$array_length,$nb_contrainte+1);
	$k=0;
	for($i=1;$i<=$array_length;$i++)
	{
		if($cout[$i]<=0)
		{
			$k++;
		}
	}
	if($k==$array_length)
	{
		return 1;
	}
	return 0;
}




////////////////////////////////Echelonnage
function echelonner($tab,$nb_variable,$nb_contrainte,$array_length)
{
	$cout=ligne($tab,$array_length,$nb_contrainte+1);
	$emplMax=emplacementMax($cout);
	$ratio=ratio($tab,$nb_variable,$nb_contrainte,$array_length);
	$emplMin=emplacementMin($ratio);
	
	$colPivot=colonne($tab,$nb_contrainte+1,$emplMax);//on ajouter +1 pour ajouter la case du cout
	$linPivot=ligne($tab,$array_length,$emplMin);
	$pivot=$tab[$emplMin][$emplMax];
	//modification ligne pivot(division)
	for($i=1;$i<=$array_length;$i++)
	{
		$tab[$emplMin][$i]=(($linPivot[$i])/$pivot);
	}
	//ajouter case pour B
	$tab[$emplMin][$array_length+1]=($tab[$emplMin][$array_length+1])/$pivot;

	print_r($pivot);
	
	for($i=1;$i<=$nb_contrainte+1+1;$i++)//ce remplissage n'inclue pas la colonne B
	{
		if($i!=$emplMin)
		{
			 for($j=1;$j<=$array_length;$j++)
		{
			$tab[$i][$j]=$tab[$i][$j]-($colPivot[$i])*($tab[$emplMin][$j]);
		}
		}
			
	}
	for($i=1;$i<=$nb_contrainte+1;$i++)//ce remplissage n'inclue pas la colonne B
	{
		if($i!=$emplMin)
		{
		//remplissage de la colonne B
		$tab[$i][$array_length+1]=$tab[$i][$array_length+1]-($colPivot[$i])*($ratio[$emplMin]);
		}
			
	}
	//ajouter la variable entrante
     $tab[$emplMin][0]=$tab[0][$emplMax];
	
	return $tab;
}

///////////////////////////////recuperer les valeurs des X///////////////////////////////
function resultatX($tab,$nb_variable,$nb_contrainte,$array_length)
{
	
for($j=1;$j<=$nb_variable;$j++)
		{
			$valeur[$j]=0;
		}
for($i=1;$i<=$nb_contrainte;$i++)
{
    if($tab[$i][0][0]=='X')
	{   
        $indice=$tab[$i][0][1];
		$valeur[$indice]=$tab[$i][1];
    }
	
}
  return $valeur;
}

///////////////////////////////recuperer les valeurs des a/////////////////////////////////
function resultatA($tab,$nb_variable,$nb_contrainte,$nbr1,$array_length)// $nbr1 = $compt1 + $compt3
{
	
for($j=1;$j<=$nb_contrainte;$j++)
		{
			$valeur[$j]=0;
		}
for($i=1;$i<=$nb_contrainte;$i++)
{
    if($tab[$i][0][0]=='a')
	{   
        $indice=$tab[$i][0][1];
		$valeur[$indice]=$tab[$i][$array_length+1];
    }
	
}
  return $valeur;
  //print_r($valeur);
}


//calcul de Za finale
function Za($tab,$nb_variable,$nb_contrainte,$nbr1,$array_length)//$nbr1 ici represente $compt2+$compt3
{
 $v=resultatA($tab,$nb_variable,$nb_contrainte,$nbr1,$array_length);
 $Za=0;
 //echo "La Solution est : ";
 
 for($i=1;$i<=$nb_contrainte;$i++)
{
    if($tab[$i][0][0]=='a')
	{   
        $indice=$tab[$i][0][1];
			$Za=$Za+$v[$indice];
	//echo "        a".$indice."=".$v[$i];
	
    }
	
}
echo "<br/>";


return $Za;
	
}




////////////////////////////echelonner le cout du premier tableau de la phase 1////////////////////////////////////
function echCout($tab,$nb_variable,$nb_contrainte,$array_length)
{
	$cout=ligne($tab,$array_length,$nb_contrainte+1);
	for($i=1;$i<=$array_length;$i++)
	{
		if($tab[$nb_contrainte+1][$i]==-1)
		{
			$indice=$tab[0][$i][1];
			for($j=1;$j<=$array_length;$j++)
			{
				$cout[$j]=$cout[$j]+$tab[$indice][$j];
			}	
			
		}
		
	}
	
  
	for($i=1;$i<=$array_length;$i++)
	{
		$tab[$nb_contrainte+1][$i]=$cout[$i];
	}
	
	return $tab;
	//print_r($indice);
	
}
//fonction qui echelonne le cout de la fin de la phase I et retourne le nouveau tableau de la phase 2
function echNewCout($NewCout,$array,$nb_variable,$nb_contrainte,$array_length)//$array_length=$compt1+$compt2
{
	$mimi=$NewCout;
	for($i=1;$i<=$nb_variable;$i++)
	{
		for($j=1;$j<=$nb_contrainte;$j++)
		{
			if($array[$j][0][0]=='X' && $array[$j][0][1]==$i)
			{
				//emplacement de la ligne avec laquelle on va echelonner
				for($k=1;$k<=$nb_variable+$array_length;$k++)//parcourir le NewCout en le echelonnant
				{
					
				     $mimi[$k]=$mimi[$k]-($NewCout[$i])*($array[$j][$k]);
				
				
				}
				
			}
		}
	}
	
	$mimi[0]="Coût";
	$mimi[$nb_variable+$array_length+1]=".";
	
	//remplir ce NewCout dans array
	for($p=0;$p<=$nb_variable+$array_length+1;$p++)
	{
		$array[$nb_contrainte+1][$p]=$mimi[$p];
	}
	
	return $array;
}


 ?>