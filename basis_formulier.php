<?php
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
	if($_SESSION['userrol']==2 || $_SESSION['userrol']==3)
	{
	$rs_voorstellingen = mysql_query("select voorstellingen.id, voorstellingen.datum as datum, voorstellingen.tijdstip, opvoeringen.naam, voorstellingen.max-coalesce((sum(reservaties.aantal)+sum(reservaties.aantalkorting)+sum(reservaties.aantalabonnement)),0) as 'beschikbaar', sum(reservaties.aantal)+sum(reservaties.aantalkorting)+sum(reservaties.aantalabonnement) as '# totaal',  sum(reservaties.aantal) as '# 10 euro', sum(reservaties.aantalkorting) as '# 9 euro', sum(reservaties.aantalabonnement) as '# 0 euro'
from voorstellingen
inner join opvoeringen on opvoeringen.id = voorstellingen.opvoeringen_id
left join reservaties on reservaties.voorstellingen_id = voorstellingen.id
where voorstellingen.datum >= curdate()
group by voorstellingen.id, voorstellingen.datum, voorstellingen.tijdstip, opvoeringen.naam
order by voorstellingen.datum") 
	or die('Error(b3.1)');
	}
	else
	{
	$rs_voorstellingen = mysql_query("select voorstellingen.id, voorstellingen.datum as datum, voorstellingen.tijdstip, opvoeringen.naam, voorstellingen.max-coalesce((sum(reservaties.aantal)+sum(reservaties.aantalkorting)+sum(reservaties.aantalabonnement)),0) as 'beschikbaar', sum(reservaties.aantal)+sum(reservaties.aantalkorting)+sum(reservaties.aantalabonnement) as '# totaal',  sum(reservaties.aantal) as '# 10 euro', sum(reservaties.aantalkorting) as '# 9 euro', sum(reservaties.aantalabonnement) as '# 0 euro'
from voorstellingen
inner join opvoeringen on opvoeringen.id = voorstellingen.opvoeringen_id
left join reservaties on reservaties.voorstellingen_id = voorstellingen.id
where voorstellingen.sold = 0
and voorstellingen.datum >= curdate()
group by voorstellingen.id, voorstellingen.datum, voorstellingen.tijdstip, opvoeringen.naam
order by voorstellingen.datum") 
	or die('Error(b3.2)');
	}

	$aantalvoorstellingen = mysql_num_rows($rs_voorstellingen);
	$naam=$_POST['naam'];
	$voornaam=$_POST['voornaam'];
	$adres=$_POST['adres'];
	$postcode=$_POST['postcode'];
	$woonplaats=$_POST['woonplaats'];
	$telefoon=$_POST['telefoon'];
	$email=$_POST['email'];
	$voorstelling=$_POST['voorstelling'];
	$opmerking=escape_quotes( $_POST['opmerking']);

	if(is_numeric($voorstelling) && $voorstelling!=0)
	{
		$rs_productie = mysql_query("select voorstellingen.id, date_format(voorstellingen.datum, '%D %d %M %Y') as datum, voorstellingen.tijdstip, opvoeringen.naam
		from voorstellingen
		inner join opvoeringen on opvoeringen.id = voorstellingen.opvoeringen_id
		where voorstellingen.id = " . $voorstelling . "
		order by voorstellingen.datum") 
		
		or die('voorstelling ' . $voorstelling . ' - Error(b4)');
		while($row = mysql_fetch_array($rs_productie))
			{
			$productie=$row['naam'] . ' - ' . strftime('%A %e %B %Y', strtotime($row['datum'])) . ' om ' . $row['tijdstip'];
			}
	}
	if(!empty($_POST['aantalplaatsen']))
		{
		$aantalplaatsen=$_POST['aantalplaatsen'];
		}
	else
		{
		$aantalplaatsen=0;
		}
	if(!empty($_POST['aantalplaatsenkorting']))
		{
		$aantalplaatsenkorting=$_POST['aantalplaatsenkorting'];
		}
	else
		{
		$aantalplaatsenkorting=0;
		}
	if(!empty($_POST['aantalplaatsenabo']))
		{
		$aantalplaatsenabo=$_POST['aantalplaatsenabo'];
		}
	else
		{
		$aantalplaatsenabo=0;
		}
	if(!empty($_POST['opmerking']))
		{
		$opmerking=escape_quotes( $_POST['opmerking']);
		}
	else
		{
		$opmerking='geen opmerking';
		}
if($aantalvoorstellingen!=0 || $_SESSION['userrol']==2 || $_SESSION['userrol']==3)
// voor bestuur (2) and webmaster (3) wordt de formulier altijd beschikbaar gesteld.
{
?>
				<form action="#" method="post">
				<fieldset>
				<legend>Online reserveringsformulier</legend>
	<?php echo $melding; ?>
	<?php echo $errorString; ?>
				<p>Velden in het <font color="maroon">rood</font> zijn verplicht in te vullen</p>
				<p><strong>Opgelet: telefonisch bestellen: 0475 523 513</strong>
				<ul>
						<li>maandag-vrijdag: 16u30-19u30</li>
						<li>weekend: 16u30-19u30</li>
				</ul>
				</p>
				<p><label for="naam"><strong>naam</strong></label> <input type="text" size="42" id="naam" name="naam" class="verplicht" style="background: maroon; color: white;" value="<?php if(empty($naam)) { echo $_SESSION['usernaam']; } else echo $naam;?>"/></p>
				<p><label for="voornaam"><strong>voornaam</strong></label> <input type="text" size="42" id="voornaam" name="voornaam" class="verplicht" style="background: maroon; color: white;" value="<?php if(empty($voornaam)){ echo $_SESSION['uservoornaam']; } else echo $voornaam;?>"/></p>
				<p><label for "adres"><strong>adres</strong></label> <input type="text" size="42" id="adres" name="adres" value="<?php if(empty($adres)){ echo $_SESSION['useradres']; } else echo $adres;?>" /></p>
				<p><label for "postcode"><strong>woonplaats</strong></label> <input type="text" size="10" id="postcode" name="postcode" value="<?php if(empty($postcode)){ echo $_SESSION['userpostcode']; } else echo $postcode;?>"/>&nbsp;<input type="text" size="27" id="woonplaats" name="woonplaats" value="<?php if(empty($woonplaats)){ echo $_SESSION['userwoonplaats']; } else echo $woonplaats;?>"/></p>
				<p><label for "telefoon"><strong>telefoon</strong></label> <input type="text" size="42" id="telefoon" name="telefoon" class="verplicht" style="background: maroon; color: white;" value="<?php if(empty($telefoon)){ echo $_SESSION['usertelefoon']; } else echo $telefoon;?>"/></p>
				<p><label for "email"><strong>e-mail</strong></label> <input size="42" id="email" name="email" type="email" class="verplicht" style="background: maroon; color: white;" value="<?php if(empty($email)) { echo $_SESSION['useremail']; } else echo $email;?>"/></p>
				<p><label for "voorstelling"><strong>voorstelling</strong></label> <select name="voorstelling" id="voorstelling" size="1" class="verplicht" style="background: maroon; color: white;">
									<option>---- kies hier uw voorstelling ----</option>
									<?php
									if(!is_numeric($_POST['voorstelling']) || !isset($_POST['voorstelling']) || !isset($_GET['voorstelling']))
										{
										while($row = mysql_fetch_array($rs_voorstellingen))
											{
											$beschikbaar=$row['beschikbaar'];
											if ($beschikbaar >0)
												{
												echo '<option value="' . $row['id'] . '">' . $row['naam'] . ' - ' . strftime('%A %e %B %Y', strtotime($row['datum'])) . ' om ' . $row['tijdstip'] . ' (' . $row['beschikbaar'] . ' vrij)</option>';
												}
											else
												{
												echo '<option disabled="disabled" value="' . $row['id'] . '">' . $row['naam'] . ' - ' . strftime('%A %e %B %Y', strtotime($row['datum'])) . ' om ' . $row['tijdstip'] . ' (volzet)</option>';
												}
											
											}
										}
									else
										{
											echo '<option value="' . $productie . '">' . $productie . '</option>';							
											while($row = mysql_fetch_array($rs_productie))
												{
												echo '<option value="' . $row['id'] . '">' . $row['naam'] . ' - ' . $row['datum'] . ' om ' . $row['tijdstip'] . '</option>';
												}
										}
								?></select></p>
				<p><label for "aantalplaatsen" class="wide"><strong>aantal volwassenen &euro; 10,00</strong></label> <input name="aantalplaatsen" type="text" class="verplicht" id="aantalplaatsen" style="background: maroon; color: white;" value="<?php echo $aantalplaatsen;?>" size="2" maxlength="2"/> 
				</p>
				<p>
				  <label for "aantalplaatsenkorting" class="wide"><strong>aantal 60+/-21 OpenDoek &euro; 9,00</strong></label> <input name="aantalplaatsenkorting" type="text" class="verplicht" style="background: maroon; color: white;" value="<?php echo $aantalplaatsenkorting;?>" size="2" maxlength="2"/> 
				</p>
				<p><label for "aantalplaatsenabo" class="wide"><strong>aantal vrijkaart - &euro; 0,00</strong></label> <input name="aantalplaatsenabo" type="text" class="verplicht" style="background: maroon; color: white;" value="<?php echo $aantalplaatsenabo;?>" size="2" maxlength="2"/> 
				</p>
				<p><label for "opmerking"><strong>opmerking</strong></label> <textarea name="opmerking" cols="40" rows="5"><?php echo $opmerking;?></textarea> <br />geef hier je opmerkingen voor de reservatie in (max 255 karakters) 
				</p>
				<p align="center"><input type="submit" name="submit" value="Klik hier om uw reservatie te bevestigen" class="submit"></p>
				<p>&nbsp;</p>
				</fieldset>
				</form>
<?php
}
else
{
echo '<p>Er zijn momenteel geen voorstellingen meer waarvoor je online kan reserveren.</p>';
echo '<p><strong>Neem telefonisch contact voor meer info: 0475 523 513</strong>
		<ul>
			<li>maandag-vrijdag: <strong>16u30-19u30</strong></li>
			<li>weekend: <strong>16u30-19u30</strong></li>
		</ul>
		</p>';
}
?>
