<div id="content_center">

<?php
	session_start();
//	header('Location:http://www.hetvierdeoor.be/mijnoor/index.php?i=loginok');
	require_once('../lib/data.inc');
	require_once('../lib/datatable.php');
	require_once('../lib/functions.php');

setlocale(LC_ALL,'nl_BE');
$global_dbh = mysql_connect($hostname, $username, $password);
if (!$global_dbh)
	{
		die('Error(b1)');
	}

$db_selected = mysql_select_db($db, $global_dbh);
if (!$db_selected)
	{
		die ('Error(b2)');
	}

if(isset($_GET['reservationid']))
	{	
		$_SESSION['reservationid'] = $_GET['reservationid'];
	}
else
	{
		//$_SESSION['reservationid'] = 0;
	}

if(!isset($_GET['seat']) && !isset($_GET['action']))
{
	unset($_SESSION['yourseats']);
	$rs_gereserveerdezetels = mysql_query("SELECT reservaties_id, zetels_id, concat(zetels.rijletter, zetels.stoelnummer) as zetel 
											FROM zetelreservaties 
											inner join zetels on zetels.id = zetelreservaties.zetels_id
											where reservaties_id = '" . $_SESSION['reservationid'] . "'") 
	or die('Error(gereserveerdezetels)');
	while($row = mysql_fetch_array($rs_gereserveerdezetels))
	{
		$seat = $row['zetel'];
		$cart = $_SESSION['yourseats'];
		$cart[] = $seat;
		$_SESSION['yourseats'] = $cart;
	}
}

if(isset($_GET['seat']))
{
	$cart = $_SESSION['yourseats'];
	$seat = $_GET['seat'];
	if(in_array($seat, $cart))
	{
		$cart= array_diff($cart, array($seat));
	}
	else
	{
		$cart[]= $seat;
	}
	$_SESSION['yourseats'] = $cart;
}
 
if($_SESSION['userrol']==2 || $_SESSION['userrol']==3)
{
$rs_rijen = mysql_query("select distinct rijletter from zetels order by rijletter desc") 
or die('Error(b3)');
}
else
{
$rs_rijen = mysql_query("select distinct rijletter from zetels order by rijletter desc") 
or die('Error(b3)');
}

$aantalrijen = mysql_num_rows($rs_rijen);

$rs_reservatie = mysql_query("SELECT opvoeringen.naam, voorstellingen.datum, aantal + aantalkorting + aantalabonnement AS maxnumber
								FROM reservaties
								INNER JOIN voorstellingen ON voorstellingen.id = reservaties.voorstellingen_id
								INNER JOIN opvoeringen ON opvoeringen.id = voorstellingen.opvoeringen_id
								WHERE reservaties.id = '" . $_SESSION['reservationid'] . "'") 
or die('Error(b3)');

while($row = mysql_fetch_array($rs_reservatie))
	{
	$aantalplaatsen = $row['maxnumber'];
	$opvoering = $row['naam'];
	$datum = strftime('%A %e %B %Y', strtotime($row['datum']));
	}

$numberofseatsforyourbasket = count($_SESSION['yourseats']);
if($aantalplaatsen > $numberofseatsforyourbasket)
{
	$allowed = 1;
}
else
{
	$allowed = 0;
}
?>

<?php
if(!isset($_GET['action'])) // er is nog niet op bevestigen gedrukt dus je bent nog bezig met het zetelplan
{
?>
	<form id="seatplan">
		<table style="color:white;" cellpadding="2" cellspacing="0">
			<tr>
				<td colspan="21" bgcolor="#800000" style="padding:10px;font-size:0.8em">U reserveert momenteel zetels voor 
					<?php echo $opvoering . ' op ' . $datum;
					?>
					<br />Klik op de zetel om deze te selecteren. Indien je de zetel niet wil, klik je er opnieuw op.
					<br />U heeft al <?php echo $numberofseatsforyourbasket; ?> van de <?php echo $aantalplaatsen; ?> plaatsen gekozen.
					&nbsp;
					<?php
					if($numberofseatsforyourbasket!=0 && $aantalplaatsen!=0 && $numberofseatsforyourbasket!=$aantalplaatsen)
						{
						$rest = $aantalplaatsen-$numberofseatsforyourbasket;
						echo 'Nog ' . $rest . ' te gaan';
						}
					elseif($numberofseatsforyourbasket==0 && $numberofseatsforyourbasket!=$aantalplaatsen)
						{
						$rest = $aantalplaatsen-$numberofseatsforyourbasket;
						echo 'Nog ' . $rest . ' te gaan';
						}
					else
						{
						echo '<input 
									name="confirmseatreservation" 
									type="button" 
									value="Klik hier om te bevestigen"
									onClick="window.location = \'http://www.hetvierdeoor.be/reserveren/index.php?i=seats&reservationid=' . $_SESSION['reservationid'] . '&action=confirm\'" >';
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="21">&nbsp;</td>
			</tr>
			<?php
				if($rs_rijen!=0)
					{
					while($row = mysql_fetch_array($rs_rijen))
						{
						$rijletter = $row['rijletter'];
						$reservationid = $_SESSION['reservationid'];
						echo '<tr id="' . $rijletter . '"><td>' . $rijletter . '</td>';
						$rs_zetels = mysql_query("select distinct stoelnummer, 
													case
														when myseats.reservaties_id = '" . $reservationid . "' then 1
														when myseats.reservaties_id > 0 then 2
														else 0 end
													as stoelgereserveerd,
													concat(myseats.naam, ' ', myseats.voornaam) as reserveerder,
													myseats.opmerking
													from zetels
													left join (select zetels_id, reservaties_id, reservaties.naam, reservaties.voornaam, reservaties.opmerking
																from zetelreservaties
																inner join reservaties on reservaties.id = zetelreservaties.reservaties_id
																where reservaties_id in 
																		(select id 
																			from reservaties 
																			where voorstellingen_id in 
																				(select voorstellingen_id 
																					from reservaties where id = '" . $reservationid . "'
																				)
																		)
																) as myseats on myseats.zetels_id = zetels.id
													where rijletter = '" . $rijletter . "' order by stoelnummer") 
						or die('Error(z1)');
						while($rowchairs = mysql_fetch_array($rs_zetels))
							{
							$stoelnummer = $rowchairs['stoelnummer'];
							$reservatiestatus = $rowchairs['stoelgereserveerd'];
							$reserveerder = $rowchairs['reserveerder'];
							$opmerking = $rowchairs['opmerking'];

							if($reservatiestatus==1 && ! in_array($rijletter . $stoelnummer, $_SESSION['yourseats']))
							{
								$seat = $rijletter . $stoelnummer;
								if ($seat != $_GET['seat'] && ! in_array($rijletter . $stoelnummer, $_SESSION['yourseats']))
								{
									//$cart = $_SESSION['yourseats'];
									//$cart[] = $seat;
									//$_SESSION['yourseats'] = $cart;
								}
							}
							$yourbasket = $_SESSION['yourseats'];

							echo '<td align="center" valign="center">
									<div>
										<input 
											name="' . $rijletter . $stoelnummer . '" 
											type="button" value="' . $rijletter . $stoelnummer . '"';
											if($_SESSION['userrol']==2 || $_SESSION['userrol']==3)
											{
											echo ' title="zichtbaar voor bestuur ==> gereserveerd door ' . $reserveerder . ' - opmerking: ' . $opmerking . '"';
											}
											echo ' onClick="window.location = \'http://www.hetvierdeoor.be/reserveren/index.php?i=seats&reservationid=' . $_SESSION['reservationid'] . '&seat=' . $rijletter . $stoelnummer . '\'" 
											';
											if($reservatiestatus==2 || ($_SESSION['userrol']!=2 && $_SESSION['userrol']!=3 && $rijletter == 'A'))
											 	{
													echo 'class="seattakenbyother" disabled ';
												}
											elseif (in_array($rijletter . $stoelnummer, $yourbasket))
												{
													echo 'class="seattakenbyyou"';
												}
											else
												{
													echo 'class="freeseat"';
													if($allowed==0)
													{
													echo ' disabled ';
													}
												}
											echo '/>
									</div>
								  </td>';
							}
						echo '</tr>';
						}
					}
			?>
			<tr>
				<th colspan="21">&nbsp;</th>
			</tr>
			<tr>
				<th colspan="21" bgcolor="#800000">het podium</th>
			</tr>
			<tr>
				<th colspan="21">&nbsp;</th>
			</tr>
			<tr>
				<td colspan="21" bgcolor="#800000">
					<div align="center">Legende&nbsp;
						<input name="freeseat" type="button" disabled class="freeseat" value="" />&nbsp;vrije zetel&nbsp;
						<input name="seattakenbyother" type="button" disabled class="seattakenbyother" value="" />&nbsp;bezet&nbsp;
						<input name="seattakenbyyou" type="button" disabled class="seattakenbyyou" value="" />&nbsp;uw reservatie&nbsp;
					</div>
				</td>
			</tr>			
			<tr>
				<td colspan="21">
					<?php
						$a = $_SESSION['yourseats'];
						//print_r ($a);
						//echo "<br />" . $aantalplaatsen . "<br />" . $allowed . "<br />";
					?><br />
				</td>
			</tr>
		</table>
	</form>
<?php
}
else // alle zetels zijn aangeklikt en je hebt op bevestigen geklikt 
{
	$reservationid = $_SESSION['reservationid'];

	$rs_deleteseats = mysql_query("delete from zetelreservaties where reservaties_id = '" . $reservationid . "'") 
	or die('Error(b3)');

	echo '<div id="form_error"><table width="700" align="left" cellpadding="0" cellspacing="0" border="0"><tr><td><img src="/images/reservation_ok.png" /></td><td><div class="succes">Uw zetelreservatie is gebeurd.<br />Hieronder vindt u de zetels die u heeft gekozen voor deze reservatie.<br /><br />';
	
	$yourcart = $_SESSION['yourseats'];	
	$arrlength=count($yourcart);
	
	//for($x=0;$x<$arrlength;$x++)
	foreach($yourcart as $value)

	  {
		$rs_insertseats = mysql_query("insert into zetelreservaties (zetels_id, reservaties_id)
										select zetels.id, " . $reservationid . "
											from zetels 
											where concat(zetels.rijletter, zetels.stoelnummer) = '" . $value . "'") 
		or die('Error(insert3)');
		echo ' zetel ' . $value . ' -/-';
	  }
	echo '<br /><br />Als webmaster / reservatieverantwoordelijke heb je gedaan wat je moest doen.';
	echo '<br />We hopen dat u verder geniet van de dag.</div></td></tr></table></div>';
	unset($_SESSION['yourseats']);		

}
?>
</div>