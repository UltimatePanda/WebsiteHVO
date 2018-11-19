		  <div id="content_center">
			<!--<div class="post">-->
			<?php
			setlocale(LC_ALL,'nl_BE');

			$global_dbh = mysql_connect($hostname, $username, $password);
			if (!$global_dbh)
				{
					die('Error(r1)');
				}
			
			$db_selected = mysql_select_db($db, $global_dbh);
			if (!$db_selected)
				{
					die ('Error(r2)');
				}
			
				// controle van het posten van het formulier
				$naam=$_POST['naam'];
				$insertnaam = str_replace("'", "''", $naam);
				$voornaam=$_POST['voornaam'];
				$insertvoornaam = str_replace("'", "''", $voornaam);
				$adres=$_POST['adres'];
				$insertadres= str_replace("'", "''", $adres);
				$postcode=$_POST['postcode'];
				$woonplaats=$_POST['woonplaats'];
				$insertwoonplaats= str_replace("'", "''", $woonplaats);
				$telefoon=$_POST['telefoon'];
				$email=$_POST['email'];
				$voorstelling=$_POST['voorstelling'];
				$opmerking=$_POST['opmerking'];
				$insertopmerking= str_replace("'", "''", $opmerking);
				if($voorstelling!=0)
				{
					$rs_productie = mysql_query("select voorstellingen.id, voorstellingen.datum, voorstellingen.tijdstip, opvoeringen.naam, voorstellingen.max-coalesce((sum(reservaties.aantal)+sum(reservaties.aantalkorting)+sum(reservaties.aantalabonnement)),0) as 'beschikbaar'
					from voorstellingen
					inner join opvoeringen on opvoeringen.id = voorstellingen.opvoeringen_id
					left join reservaties on reservaties.voorstellingen_id = voorstellingen.id
					where voorstellingen.id = " . $voorstelling . "
					order by voorstellingen.datum") 
					
					or die('Error(r3)');
					while($row = mysql_fetch_array($rs_productie))
						{
						$productie=$row['naam'] . ' - ' . strftime('%A %e %B %Y', strtotime($row['datum'])) . ' om ' . $row['tijdstip'];
						$beschikbaar=$row['beschikbaar'];
						}
				}
				if(!empty($_POST['aantalplaatsen']))
					{
						if(strlen($_POST['aantalplaatsen'])<1)
							{
							$aantalplaatsen=0;
							}
							else
							{
							$aantalplaatsen=$_POST['aantalplaatsen'];
							}
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
				$totaal=$aantalplaatsen+$aantalplaatsenkorting+$aantalplaatsenabo;
				$error=0;
				
				if(isset($_POST['submit']))
					{
						$allowedFields = array(
							'naam',
							'voornaam',
							'adres',
							'postcode',
							'woonplaats',
							'telefoon',
							'email',
							'voorstelling',
							'aantalplaatsen',
							'aantalplaatsenkorting',
							'aantalplaatsenabo',
							'opmerking',
						);
						
						$requiredFields = array(
							'naam' => 'naam is verplicht.',
							'voornaam' => 'voornaam is verplicht.',
							'telefoon' => 'telefoon is verplicht.',
							'email' => 'e-mailadres is verplicht.',
							'voorstelling' => 'kies uw voorstelling.',
						);
		
						$errors = array();
						
						// We need to loop through the required variables to make sure they were posted with the form.
						foreach($requiredFields as $fieldname => $errorMsg)
						{
							if(empty($_POST[$fieldname]))
							{
								$errors[] = $errorMsg;
							}
						}

						if (strlen($_POST[email]) < 6 || !preg_match('/^([a-z0-9])+([a-z0-9\._-])*@([a-z0-9_-])+([a-z0-9\._-]+)+$/i', $_POST[email]))
							//e-mail is fout
							{
							$errorMsg='verifieer het e-mailadres';
							$errors[] = $errorMsg;
							}

						if(empty($_POST['aantalplaatsen']) && empty($_POST['aantalplaatsenkorting']) && empty($_POST['aantalplaatsenabo']))
							{
								$errorMsg = 'gelieve het aantal plaatsen te verifi&euml;ren';
								$errors[] = $errorMsg;
							}

						if((!empty($_POST['aantalplaatsen']) && (!is_numeric($_POST['aantalplaatsen']) || $_POST['aantalplaatsen']>99)) || (!empty($_POST['aantalplaatsenkorting']) && (!is_numeric($_POST['aantalplaatsenkorting']) || $_POST['aantalplaatsenkorting']>99)) || (!empty($_POST['aantalplaatsenabo']) && (!is_numeric($_POST['aantalplaatsenabo'])) || $_POST['aantalplaatsenabo']>99))
							{
								$errorMsg = 'aantal plaatsen moet een getal zijn tussen 1 en 99';
								$errors[] = $errorMsg;
							}

						if(empty($_POST['voorstelling']) || (!is_numeric($_POST['voorstelling'])))
							{
								$errorMsg = 'gelieve de gekozen voorstelling te verifi&euml;ren.';
								$errors[] = $errorMsg;
							}

						if($totaal>$beschikbaar)
							{
								$errorMsg = 'slechts ' . $beschikbaar . ' plaats(en) beschikbaar en je reserveert er ' . $totaal . '!';
								$errors[] = $errorMsg;
							}

						// Loop through the $_POST array, to create the PHP variables from our form.
						foreach($_POST AS $key => $value)
						{
							// Is this an allowed field? This is a security measure.
							if(in_array($key, $allowedFields))
							{
								${$key} = $value;
							}
						}
						
						// Waren er fouten?				
						if(count($errors) > 0)
						{
							$errorString .= '<div id="form_error"><table width="700" align="left" cellpadding="0" cellspacing="0" border="0"><tr><td><img src="/images/reservation_error.png" /></td><td><div class="error"><ul>';
							foreach($errors as $error)
							{
								$errorString .= "<li>$error</li>";
							}
							$errorString .= '</ul></div></td></tr></table></div>';
						}
					}
							
				if(!isset($_POST['submit']))
				//er is nog niet op verzenden gedrukt dus gewoon het basis-formulier geven
					{
					include('basis_formulier.php');
					}
				elseif(count($errors)>0)
				// er klopt nog iets niet in de ingegeven velden
					{
					include('basis_formulier.php');
					}
				else
				// alles zou in orde moeten zijn dus verstuur de mail naar resevatie én naar de reservateur 
				// en geef de ok pagina maar 
					{
					//de mail naar degene die reserveert
					$inhoud_mail = '<html><head>
<title>Reservatie</title>
</head>
<body align="center">
<table align="center"><tr><td align="center">
<table cellpadding="10px" width="750px" style="font-size:14px;font-family:Century Gothic,Verdana,Helvetica,Arial,sans-serif">
  <tr><td height="100" colspan="2"><a href="http://www.hetvierdeoor.be/mijnoor/index.php?i=register"><img src="http://www.hetvierdeoor.be/images/navigation_banner_register.png"></a></td>
					</tr>
					<tr><td colspan="2">Beste ' . $voornaam . ' ' . $naam . ',<br /><br /> u heeft zonet gereserveerd voor een productie van Het Vierde Oor.<br /> Hieronder sturen wij u de door u ingegeven informatie.<br /><br /></td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>naam & voornaam</strong></td><td style="border-bottom:1px;border-bottom-color:maroon;">' . $naam . ' ' . $voornaam . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>adres</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $adres . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>postcode & woonplaats</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $postcode . ' ' . $woonplaats . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>telefoon</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $telefoon . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>e-mail</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $email . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>voorstelling</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $productie . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>aantal plaatsen volwassenen - 10,00 euro</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $aantalplaatsen . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>aantal plaatsen 60+/-21 & OpenDoek - 9,00 euro</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $aantalplaatsenkorting . '</td></tr> 
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>aantal plaatsen vrijkaarten (0,00 euro)</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $aantalplaatsenabo . '</td></tr>
					<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>uw opmerking</strong></td>
					<td style="border-bottom-color:maroon;border-bottom:1px">' . $opmerking . '</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr> 
					<tr><td colspan="2">Onze reservatieverantwoordelijke zal deze reservatie behandelen en u per mail bevestigen.<br /><br />Geniet verder nog van je dag.<br /><br />PS. Om gemakkelijker te reserveren, kan je je ook registreren op onze website.<br />
					Ga naar <a href="http://www.hetvierdeoor.be/mijnoor/index.php?i=register">http://www.hetvierdeoor.be/mijnoor/index.php?i=register</a>, en geef je informatie in.<br />
					<br />
					<img src="http://www.hetvierdeoor.be/images/reservation_ok.png">Reservatie Het Vierde Oor</td></tr>
					</table></td></tr></table>
					</body></html>'; 
					
					// -------------------- 
					// spambot protectie 
					// ------ 
					// van de tutorial: http://www.phphulp.nl/php/tutorials/10/340/ 
					// ------ 
					
					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: Reservatie Het Vierde Oor vzw <reservatie@hetvierdeoor.be>'  . "\r\n";
					$headers .= 'Reply-To: reservatie@hetvierdeoor.be' . "\r\n";
					//$headers .= 'Bcc: webmaster@hetvierdeoor.be'  . "\r\n";
					$headers .= 'Date: '.date("r")."\r\n";
					//$headers = stripslashes($headers);
					//$headers = str_replace("\n", "", $headers); // Verwijder \n 
					//$headers = str_replace("\r", "", $headers); // Verwijder \r 
					//$headers = str_replace("\"", "\\\"", str_replace("\\", "\\\\", $headers)); // Slashes van quotes 
   
					mail($email, "Uw reservatie via www.hetvierdeoor.be", $inhoud_mail, $headers); 

					//de mail naar de reservatieverantwoordelijke
					$inhoud_mail = '<html><head><title>Reservatie</title>
</head>
<body align="center" font-size="10px">
<table align="center"><tr><td>
<table cellpadding="10px" style="font-family:Century Gothic,Verdana,Helvetica,Arial,sans-serif;font-size=11px";>
<tr style="background-image:url(http://www.hetvierdeoor.be/images/navigation_banner.png);" height:100px	><td height="100" colspan="2">&nbsp;</td>
					</tr>
<tr><td colspan="2">Beste,<br /><br />onderstaande reservatie werd zonet uitgevoerd via de website.<br /><br /></td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>naam & voornaam</strong></td><td style="border-bottom:1px;border-bottom-color:maroon;"> ' . $naam . ' ' . $voornaam . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>adres</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $adres . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>postcode & woonplaats</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $postcode . ' ' . $woonplaats . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>telefoon</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $telefoon . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>e-mail</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $email . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>voorstelling</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $productie . '</td></tr>  
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>aantal plaatsen volwassenen - 10,00 euro</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $aantalplaatsen . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>aantal plaatsen 60+/-21 & OpenDoek - 9,00 euro</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $aantalplaatsenkorting . '</td></tr> 
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>aantal plaatsen vrijkaarten (0,00 euro)</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $aantalplaatsenabo . '</td></tr>
<tr><td bgcolor="maroon" style="color:white; padding:10px" align="right"><strong>opmerking</strong></td>
<td style="border-bottom-color:maroon;border-bottom:1px">' . $opmerking . '</td></tr>
<tr><td colspan="2">&nbsp;</td></tr> 
<tr><td colspan="2">Kan jij als reservatieverantwoordelijke deze reservatie behandelen en de afzender per mail bevestigen?<br />Geniet verder nog van je dag.<br /><br /> 
<img src="http://www.hetvierdeoor.be/images/reservation_ok.png">Verstuurd vanop het reservatieformulier van de website van Het Vierde Oor</td></tr></table></td></tr></table></body></html> 
'; 
					
					// -------------------- 
					// spambot protectie 
					// ------ 
					// van de tutorial: http://www.phphulp.nl/php/tutorials/10/340/ 
					// ------ 
					
					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: ' .$voornaam . ' ' . $naam . '<webmaster@hetvierdeoor.be>' . "\r\n";
					$headers .= 'Reply-To: ' . $email .  "\r\n";
					//$headers .= 'Bcc: webmaster@hetvierdeoor.be'  . "\r\n";
    				$headers .= 'Date: '.date("r")."\r\n";

					mail("reservatie@hetvierdeoor.be", "Nieuwe reservatie via www.hetvierdeoor.be", $inhoud_mail, $headers); 
					
					// de reservatie in de database opslaan
					include('../lib/data.inc');
					//echo 'de hostname: ' . $hostname;

					$global_dbh = mysql_connect($hostname, $username, $password);
					if (!$global_dbh)
						{
						    die('Uw reservatiemail is verstuurd (1).');
						}

					$db_selected = mysql_select_db($db, $global_dbh);
					if (!$db_selected)
						{
						    die ('Uw reservatiemail is verstuurd (2).');
						}
				
					// reservatie toevoegen
					$result = mysql_query("insert into reservaties (email, voorstellingen_id, aantal, aantalkorting, aantalabonnement, adres, postcode, woonplaats, telefoon, naam, voornaam, opmerking)
											values ('" . $email . "', '" . $voorstelling . "', '" . $aantalplaatsen . "', '" . $aantalplaatsenkorting . "', '" .$aantalplaatsenabo . "', '" . $insertadres . "', '" . $postcode . "', '" . $insertwoonplaats . "', '" . $telefoon . "', '" . $insertnaam . "', '" . $insertvoornaam . "', '" . $insertopmerking . "')") 
				
					or die('Error(3): Gelieve mailtje te sturen naar webmaster@hetvierdeoor.be - ' . mysql_error());  

					// id van de reservatie opvragen
					$rs_reservation = mysql_query("select max(id) as reservationid from reservaties where email='" . $email . "' and voorstellingen_id = '" . $voorstelling . "'")
					or die('Error(4): Gelieve mailtje te sturen naar webmaster@hetvierdeoor.be met melding "ik kan de nummer niet ophalen"');  
					while($row = mysql_fetch_array($rs_reservation))
						{
						$reservationid = $row['reservationid'];
						}
					// de ok-pagina
					$melding='<div id="form_error">
								<table width="700" align="left" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td><img src="/images/reservation_ok.png" /></td>
										<td colspan="2">
											<div class="succes">
												Bedankt voor uw reservatie.<br />
												Onderstaande zijn de gegevens die u ons heeft doorgegeven. U ontvangt deze over enkele ogenblikken ook per e-mail.<br />
												Zodra uw reservatie behandeld is ontvangt u van ons een bevestigingsmail.<br /><br />
</div>
										</td>
									</tr>
								</table>
							</div>';
					include('geslaagd.php');
					}
			?>
			<!--</div>-->
<!--
												'
												//<input 
												//name="seatreservation" 
												//type="button" 
												//value="Klik hier om uw zetels te reserveren"
												//onClick="window.location = \'http://www.hetvierdeoor.be/reserveren/index.php?i=seats&reservationid=' . $reservationid . '\'" >
											'
-->		</div>