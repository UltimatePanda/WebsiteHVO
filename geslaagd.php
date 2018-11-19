<?php
	setlocale(LC_ALL,'nl_BE');
	$naam=$_POST['naam'];
	$voornaam=$_POST['voornaam'];
	$adres=$_POST['adres'];
	$postcode=$_POST['postcode'];
	$woonplaats=$_POST['woonplaats'];
	$telefoon=$_POST['telefoon'];
	$email=$_POST['email'];
	$voorstelling=$_POST['voorstelling'];
	$opmerking=escape_quotes( $_POST['opmerking']);

	$rs_productie = mysql_query("select voorstellingen.id, voorstellingen.datum, voorstellingen.tijdstip, opvoeringen.naam
	from voorstellingen
	inner join opvoeringen on opvoeringen.id = voorstellingen.opvoeringen_id
	where voorstellingen.id = " . $voorstelling . "
	order by voorstellingen.datum") 
	
	or die('Error(g3)');
	while($row = mysql_fetch_array($rs_productie))
		{
		$productie=$row['naam'] . ' - ' . strftime('%A %e %B %Y', strtotime($row['datum'])) . ' om ' . $row['tijdstip'];
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
?>
				<form>
				<fieldset>
				<legend>Online reserveringsformulier</legend>
<?php echo $melding;?>
				<p><label><strong>naam</strong>:</label> <?php echo $naam;?></p>
				<p><label><strong>voornaam</strong>:</label> <?php echo $voornaam;?></p>
				<p><label><strong>adres</strong>:</label> <?php echo $adres;?></p>
				<p><label><strong>woonplaats</strong>:</label> <?php echo $postcode;?>&nbsp;<?php echo $woonplaats;?></p>
				<p><label><strong>telefoon</strong>:</label> <?php echo $telefoon;?></p>
				<p><label><strong>e-mail</strong>:</label> <?php echo $email;?></p>
				<p><label><strong>productie</strong>:</label> <?php echo $productie;?></p>
				<p><label class="wide"><strong>aantal volwassenen - &euro; 10,00 </strong>:</label> <?php echo $aantalplaatsen;?></p>
				<p><label class="wide"><strong>aantal 60+/-21, OPENDOEK - &euro; 9,00 </strong>:</label> <?php echo $aantalplaatsenkorting;?></p>
				<p><label class="wide"><strong>aantal vrijkaart - &euro; 0,00 </strong>:</label> <?php echo $aantalplaatsenabo;?></p>
				<p><label class="wide"><strong>opmerking </strong>:</label> <?php echo $opmerking;?></p>
				</fieldset>
				</form>
