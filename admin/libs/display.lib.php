<?php
function displayShowTierConfig($TierNro, $Platform, $Size, $Ajax = 0, $idTier = 0, $PostData = false){
	global $db;
	if(!$Ajax){ echo "<div id=\"tier$TierNro\">"; }
	
	if($idTier == 0 && $PostData !== false){
		$Floor = $PostData['Floor'];
	}elseif($idTier > 0){
		$sql = "SELECT * FROM " . ADUNITSTIERS . " WHERE id = '$idTier' LIMIT 1";
		$query = $db->query($sql);
		$tierData = $db->fetch_array($query);
		
		$TierNro = $tierData['Tier'];
		$Floor = $tierData['Floor'];
	}else{
		$Floor = 1;
	}
	
	?>	
	<!-- TIER STATS -->
	<div class="clsd-fx clmd06" style="float: left;">
		<div class="clmd12">
			<h3>Tier <?php echo $TierNro; ?></h3>
		</div>
		<div class="clmd02">
			<!--<Floor>-->
			<div class="frm-group d-fx lbl-lf">
				<label>Floor</label>
				<div class="d-flx1">
					<label class="lbl-icon ncn-lf">
						<input type="text" name="floor-<?php echo $TierNro; ?>" value="<?php echo $Floor; ?>" style="padding-right:10px;" class="numeric" />
					</label>
				</div>
			</div>
			<!--</Floor>-->
		</div>
		<div class="clmd04">
			<!--<Bidders>-->
			<div class="frm-group d-fx lbl-lf">
				
				<div class="d-flx1">
					<ul style="list-style: none;">
						<li><strong>Bidders</strong></li>
					<?php
						$jQueryTags = '';
						$TextAreas = '';
						$FirstText = '';
						$sql = "SELECT * FROM " . BIDDERS . " WHERE Deleted = 0 AND Active = 1 ORDER BY Name ASC";
						$query = $db->query($sql);
						if($db->num_rows($query) > 0){
							while($Bidder = $db->fetch_array($query)){
								?><li><a <?php if($FirstText == ''){ echo 'style="color:#c80068;" '; } ?>href="#<?php echo $Bidder['Code']; ?>-<?php echo $TierNro; ?>" class="selectbid-<?php echo $TierNro; ?>"><?php echo $Bidder['Name']; ?></a></li><?php
								$TextAreas .= '<textarea id="' . $Bidder['Code'] . '-' . $TierNro . '" name="bidder-' . $Bidder['Code'] . '-' . $TierNro . '" class="textareacodes-' . $TierNro . '" rows="1" style="height:100px;"></textarea>';
								
								$NamesList = '';
								$DefaultsList = '';
								$Co = '';
								$Co2 = '';
								$sql2 = "SELECT Name, isDefault FROM " . PLACEMENTS . " WHERE Deleted = 0 AND idBidder = '" . $Bidder['id'] . "' AND Platform = '$Platform' AND Size = '$Size'";
								$query2 = $db->query($sql2);
								if($db->num_rows($query2) > 0){
									while($Place = $db->fetch_array($query2)){
										$NamesList .= "$Co'".$Place['Name']."'";
										$Co = ",";
										if($Place['isDefault'] == 1){
											$DefaultsList .= "$Co2'".$Place['Name']."'";
											$Co2 = ",";
										}
									}
								}
								
								if($FirstText == ''){
									$FirstText = $Bidder['Code'];
								}
								
								if($idTier == 0){
									//SET POST TAGS
									$LoadTags = '[]';
									if($PostData !== false){
										if(isset($PostData['Placements'][$Bidder['Code']])){
											$LoadTags = $PostData['Placements'][$Bidder['Code']];											
										}
									}else{
										//DEFAULT TAGS IF NEW TIER
										$LoadTags = '[' . $DefaultsList . ']';
									}
								}else{
									//LOAD DB TAGS
									$LoadTags = '[';
									$sql = "SELECT id FROM " . BIDDERS . " WHERE Code = '" . $Bidder['Code'] . "'";
									$idBidder = $db->getOne($sql);
									
									$sql = "SELECT * FROM " . ADUNITSPLACE . " WHERE idTier = '$idTier'";
									$query3 = $db->query($sql);
									if($db->num_rows($query3) > 0){
										while($tierPlace = $db->fetch_array($query3)){
											$idPlacement = $tierPlace['idPlacement'];
											$sql = "SELECT idBidder, Name FROM " . PLACEMENTS . " WHERE id = '$idPlacement' LIMIT 1";
											$query4 = $db->query($sql);
											if($db->num_rows($query4) > 0){
												$PlaceData = $db->fetch_array($query4);
												if($PlaceData['idBidder'] == $idBidder){
													$LoadTags .= '"' . $PlaceData['Name'] . '"';
												}
											}
										}
									}
									$LoadTags .= ']';
								}
								
								$jQueryTags .= " $('#" . $Bidder['Code'] . "-" . $TierNro . "').textext({ plugins : 'tags autocomplete', tagsItems: " . $LoadTags . "}).bind('getSuggestions', function(e, data){
var list = [" . $NamesList . "],
textext = $(e.target).textext()[0],
query = (data ? data.query : '') || '';

$(this).trigger('setSuggestions', { result : textext.itemManager().filter(list, query) } );
}); ";
								
							}
						}
					?>
					</ul>
				</div>
			</div>
			<!--</Bidders>-->
		</div>
		<div class="clmd06">
			<div class="d-flx1">
				<ul style="list-style: none;">
					<li><strong>Placements</strong></li>
				</ul>
				<?php echo $TextAreas; ?>
			</div>
		</div>
		<?php
			
		$jQueryTags .= "$('.textareacodes-$TierNro').parents('.text-core').hide();";
		$jQueryTags .= "$('.textareacodes-$TierNro').parents('.text-core').height('100px');";
		$jQueryTags .= "$('.textareacodes-$TierNro').parents('.text-wrap').height('100px');";
		$jQueryTags .= "$('#$FirstText-$TierNro').parents('.text-core').show();";
		$jQueryTags .= "$('.selectbid-$TierNro').click(function(e){ e.preventDefault(); $('.textareacodes-$TierNro').parents('.text-core').hide(); $($(this).attr('href')).parents('.text-core').show(); $('.selectbid-$TierNro').css('color',''); $($(this)).css('color','#c80068'); });";
						
		?>	
	</div>
	<!-- TIER FINISH --><?php
	if($Ajax){
		?><script><?php echo $jQueryTags; ?></script><?php
		return '';
	}else{
		echo "</div>";
		return $jQueryTags;
	}
	
}


function displayNewDisplayAd($Platform, $idAd = 0){
	global $db;
	?><!--<Ad Units>-->
	<ul class="lst-tbs b-fx mb2" style="float:right; margin-top:-10px;">
		<li class="b-rt"><a href="#" class="fa-plus-circle" id="newadunit">Añadir Ad Unit</a></li>
	</ul>
	<div class="tbl-cn">
		<table id="tbl-adunits">
			<thead>
				<tr>
					<th>Ad Unit</th>
					<th>Tamaño</th>
					<th>Posición</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody><?php
				if($idAd == 0){
					$sql = "SELECT * FROM " . ADUNITS . " WHERE isDefault = 1 AND Deleted = 0 AND Platform = '$Platform' ORDER BY Name ASC";
				}else{
					$sql = "SELECT CCode FROM " . ADS . " WHERE id = '$idAd'";
					$AdUnits = $db->getOne($sql);
					$AdUnits = str_replace('{','',$AdUnits);
					$AdUnitsA = explode('}', $AdUnits);
					$Position = array();
					$AndId = " AND (";
					$OR = "";
					foreach($AdUnitsA as $AdUnit){
						if($AdUnit != ''){
							$AdUnitA = explode(':',$AdUnit);
							$idAdUnit = $AdUnitA[0];
							$Position[$idAdUnit] = $AdUnitA[1];
							
							$AndId .= "$OR id = '$idAdUnit' ";
							$OR = " OR";
						}
					}
					$AndId .= ")";
					$sql = "SELECT * FROM " . ADUNITS . " WHERE Deleted = 0 $AndId ORDER BY Name ASC";
				}
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					while($AdUnit = $db->fetch_array($query)){
						$sql = "SELECT Size FROM " . DSIZES . " WHERE id = '" . $AdUnit['Size'] . "' LIMIT 1";
						$Size = $db->getOne($sql);
						?><tr id="ad-<?php echo $AdUnit['id']; ?>">
							<td data-title="Nombre"><?php echo $AdUnit['Name']; ?></td>
							<td data-title="Tamano"><?php echo $Size; ?></td>
							<td data-title="Posicion"><select name="position-<?php echo $AdUnit['id']; ?>"><?php
								$sql = "SELECT id, Position FROM " . DPOSITIONS . " WHERE Platform = '$Platform'";
								$query2 = $db->query($sql);
								if($db->num_rows($query2) > 0){
									while($Pos = $db->fetch_array($query2)){
										?><option value="<?php echo $Pos['id']; ?>"<?php 
											if($idAd == 0){
												if($AdUnit['Position'] == $Pos['id']) { echo " selected"; }
											}else{
												if($Position[$AdUnit['id']] == $Pos['id']) { echo " selected"; }
											}
										?>><?php echo $Pos['Position']; ?></option><?php
									}
								}
								?></select></td>
							<td data-title="Opciones">
								<ul class="lst-opt">
									<li><a href="#ad-<?php echo $AdUnit['id']; ?>" class="fa-trash-o tt-lt delad" data-toggle="tooltip" title="" data-original-title="Quitar Ad Unit"></a></li>
								</ul>
								<input type="hidden" name="adunit[]" value="<?php echo $AdUnit['id']; ?>" />
							</td>
						</tr><?php
					}
				}
			?></tbody>
		</table>
		
		<div class="modal" id="adunits" style="display:none; margin-top:0px !important; padding-top:4rem;">
			<label class="modal__bg" onclick="$('#adunits').hide();"></label>
			<div class="modal__inner" style="width:60%">
				<label class="modal__close" onclick="$('#adunits').hide();"></label>
				<div class="bx-cn bx-shnone">
					<div class="bx-hd dfl b-fx">
						<div class="titl" style="color:#FFF !important;">Añadir Ad Unit</div>
					</div>
					<div class="bx-bd">
						<table class="tbl-payments" style="margin:0">
							<thead>
								<tr>
									<th>Ad Unit</th>
									<th>Tamaño</th>
									<th></th>
								</tr>
							</thead>
							<tbody><?php
							$sql = "SELECT * FROM " . ADUNITS . " WHERE Deleted = 0 AND Platform = '$Platform' ORDER BY Name ASC";
							$query = $db->query($sql);
							if($db->num_rows($query) > 0){
								while($AdUnit = $db->fetch_array($query)){
									$sql = "SELECT Size FROM " . DSIZES . " WHERE id = '" . $AdUnit['Size'] . "' LIMIT 1";
									$Size = $db->getOne($sql);
							?><tr>
								<td><?php echo $AdUnit['Name']; ?></td>
								<td><?php echo $Size; ?></td>
								<td>
									<ul class="lst-opt"><li><a href="<?php echo $AdUnit['id']; ?>" class="fa-plus-circle tt-lt addad" data-toggle="tooltip" title="" data-original-title="Añadir Ad Unit"></li></ul></td>
							</tr><?php
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>	
	</div>
	<!--</Ad Units>-->
	<?php
}


function newAdUnit($idAdUnit){
	global $db;
	
	$sql = "SELECT * FROM " . ADUNITS . " WHERE id = '$idAdUnit' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$AdUnit = $db->fetch_array($query);
		$Platform = $AdUnit['Platform'];
		$AdUnitVals['id'] = $AdUnit['id'];
		$AdUnitVals['Name'] = $AdUnit['Name'];
		$sql = "SELECT id, Position FROM " . DPOSITIONS . " WHERE Platform = '$Platform'";
		$query2 = $db->query($sql);
		if($db->num_rows($query2) > 0){
			$AdUnitVals['Position'] = '<select name="position-' . $AdUnit['id'] . '">';
			while($Pos = $db->fetch_array($query2)){
				$AdUnitVals['Position'] .= '<option value="' . $Pos['id'] . '"';
				if($AdUnit['Position'] == $Pos['id']) { $AdUnitVals['Position'] .= ' selected'; }
				$AdUnitVals['Position'] .= '>' . $Pos['Position'] . '</option>';
			}
			$AdUnitVals['Position'] .= '</select>';
		}
		
		$sql = "SELECT Size FROM " . DSIZES . " WHERE id = '" . $AdUnit['Size'] . "' LIMIT 1";
		$AdUnitVals['Size'] = $db->getOne($sql);
		
		echo json_encode($AdUnitVals);
	}
}

function newGenerateJS($idSite){
	global $db;
	
	$sql = "SELECT " . USERS . ".LKQD_id FROM " . USERS . " INNER JOIN " . SITES . " ON " . SITES . ".idUser = " . USERS . ".id WHERE " . SITES . ".id = '$idSite'";
	$LKQDid = $db->getOne($sql);
	
	$DefaultTags['Test'][1] = '1089817'; //Intext DT
	$DefaultTags['Test'][2] = '1089819'; //Intext MW
	$DefaultTags['Test'][3] = '1089817'; //Slider DT
	$DefaultTags['Test'][4] = '1089819'; //Slider MW
	$DefaultTags['Pending'][1] = '1089820'; //Intext DT
	$DefaultTags['Pending'][2] = '1089823'; //Intext MW
	$DefaultTags['Pending'][3] = '1089820'; //Slider DT
	$DefaultTags['Pending'][4] = '1089823'; //Slider MW
	
	$sql = "SELECT * FROM " . ADS . " WHERE idSite = '$idSite' ORDER BY Type ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "SELECT filename, eric, test FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
		$querySite = $db->query($sql);
		$SiteData = $db->fetch_array($querySite);
		
		$Filename = $SiteData['filename'];
		$TestMode = $SiteData['test'];
		$EricA = $SiteData['eric'];
		
		$arFilename = explode('/',$Filename);
		$Filename = $arFilename[3];
		
		$PrebidLoaded = false;
		$NewCode = '';
		$LoadAdUnits = array();
		
		while($Ads = $db->fetch_array($query)){
			$Type = $Ads['Type'];
			
			if($Type == 100){
				$Code = "// custome code \n\n" . $Ads['CCode'];
			}elseif($Type >= 10 && $Type < 20){
				$Code = '';
				//echo $Ads['CCode'];
				$AdUnits = str_replace('{','',$Ads['CCode']);
				$AdUnitsA = explode('}', $AdUnits);
				
				foreach($AdUnitsA as $AdUnit){
					if($AdUnit != ''){
						$AdUnitA = explode(':', $AdUnit);
						$idAdUnit = $AdUnitA[0];
						$LoadAdUnits[$idAdUnit] = $AdUnitA[1];
					}
				}
			}else{
				$sql = "SELECT Code FROM " . ADSCODES . " WHERE Type = '$Type' LIMIT 1";
				$Code = $db->getOne($sql);
				
				if($TestMode == 1){
					$Code = str_replace('#IDLKQD',$DefaultTags['Test'][$Type],$Code);
				}elseif($EricA == 1){
					$Code = str_replace('#IDLKQD',$DefaultTags['Pending'][$Type],$Code);
				}else{
					$Code = str_replace('#IDLKQD',$Ads['idLKQD'],$Code);
				}
				$Code = str_replace('#IDPLKQD',$LKQDid,$Code);
				$Code = str_replace('#divID',$Ads['divID'],$Code);
				$Code = str_replace('#Width',$Ads['Width'],$Code);
				$Code = str_replace('#Height',$Ads['Height'],$Code);
				if($Ads['Override'] == 1){ $OverrideTF = 'true'; } else { $OverrideTF = 'false'; }
				$Code = str_replace('#Override',$OverrideTF,$Code);
				if($Ads['DFP'] == 1){ $DFPTF = 'true'; } else { $DFPTF = 'false'; }
				$Code = str_replace('#DFP',$DFPTF,$Code);
				if($Ads['SPosition'] == 1){ $SposTF = 'right'; } else { $SposTF = 'left'; }
				$Code = str_replace('#Spos',$SposTF,$Code);
				if($Ads['Close'] == 1){ $CloseTF = 'true'; } else { $CloseTF = 'false'; }
				$Code = str_replace('#Close',$CloseTF,$Code);
				if($Ads['HeightA'] == 0){
					$Code = str_replace('#AA','',$Code);
				} else {
					$Code = str_replace('#AA',"\nbottomPadding: ".$Ads['HeightA'].",",$Code);
				}
			}
			
			$NewCode .= $Code . "\n\n";
		}
		//print_r($LoadAdUnits);

		if(count($LoadAdUnits) > 0){
			$N = 0;
			foreach($LoadAdUnits as $idAdUnit => $AdUnitPosition){
				$sql = "SELECT * FROM " . ADUNITS . " WHERE id = '$idAdUnit' AND Deleted = 0 LIMIT 1";
				$query2 = $db->query($sql);
				if($db->num_rows($query2) > 0){
					$AdU = $db->fetch_array($query2);
					$sql = "SELECT Size FROM " . DSIZES . " WHERE id = '" . $AdU['Size'] . "' LIMIT 1";
					$Size = $db->getOne($sql);
					
					//$AdU['Position']
					
					$sql = "SELECT * FROM " . ADUNITSTIERS . " WHERE idAdunit = '$idAdUnit'";
					$query3 = $db->query($sql);
					if($db->num_rows($query3) > 0){
						while($Tier = $db->fetch_array($query3)){
							$idTier = $Tier['id'];
							$TierNr = $Tier['Tier'];
							$Floor = $Tier['Floor'];
							$Floors[$TierNr] = $Floor;
							$sql = "SELECT idPlacement FROM " . ADUNITSPLACE . " WHERE idTier = '$idTier'";
							$query4 = $db->query($sql);
							if($db->num_rows($query4) > 0){
								while($Place = $db->fetch_array($query4)){
									$idPlace = $Place['idPlacement'];
									$sql = "SELECT * FROM " . PLACEMENTS . " WHERE Active = 1 AND Deleted = 0 AND id = '$idPlace'";
									$query5 = $db->query($sql);
									if($db->num_rows($query5) > 0){
										$PlaceData = $db->fetch_array($query5);
										$idBidder = $PlaceData['idBidder'];
										$ZoneId = $PlaceData['idZone'];
										
										$sql = "SELECT * FROM " . BIDDERS . " WHERE id = '$idBidder' AND Active = 1 AND Deleted = 0";
										$query6 = $db->query($sql);
										if($db->num_rows($query6) > 0){
											$BidderData = $db->fetch_array($query6);
											$BidderCode = $BidderData['Code'];
											$PlacementParam = $BidderData['PlacementParam'];
											$FloorParam = $BidderData['FloorParam'];
											$idBidderParam = $BidderData['idBidderParam'];
											$idBidderC = $BidderData['idBidder'];
											
											$N++;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['BidderCode'] = $BidderCode;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['ZoneId'] = $ZoneId;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['PlacementParam'] = $PlacementParam;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['FloorParam'] = $FloorParam;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['idBidderParam'] = $idBidderParam;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['idBidder'] = $idBidderC;
											$AdUData[$AdU['Platform']][$TierNr][$Size][$N]['Floor'] = $Floor;
										}
									}
								}
							}
						}
					}
				}
			}
			
			
			//$Code = file_get_contents('/var/www/html/ads/prebid/prebid2.5.1.js');
			$Code = '{PrebidCode}';
			$Code .= "\n\n";
			//print_r($AdUData);
			foreach($AdUData as $Platform => $AdUDataBP){
				//echo "=------------ $Platform --------=";
				if($Platform == 1){
					$Code .= "\n\n//DISPLAY DESKTOP STARTS\n\n";
				}else{
					$Code .= "\n\n//DISPLAY MW STARTS\n\n";
				}
				
				$TiersL = count($AdUDataBP) - 1;
				$Code .= str_replace('{Platfom}',$Platform,str_replace('{Rep}',$TiersL,file_get_contents('/var/www/html/ads/prebid/reportstats.js')));
				$Code .= "\n var vidTry = 0; var idImpresion = 0; var shown = 0; var VIDOOMY_DISPLAY_TIMEOUT = 3000; var adUnits = []; var Floors = [];";
				$AdCodes = array();
				
				//$Code .= "console.dir('Prebid loaded 1');\n";
				
				foreach($AdUDataBP as $TierNr => $TierData){
					$Code .= "\n var adUnit = [";
					$ComaCode = '';
					$NCode = 0;
					foreach($TierData as $Size => $BannerData){
						$NCode++;
						$Size = str_replace('x', ', ', $Size);
						$AdCode = "vAd$TierNr-$NCode";
						$AdCodes[] = $AdCode;
						$Code .= "$ComaCode{code: '$AdCode', mediaTypes: { banner: { sizes: [[$Size]] } }, bids: [";
						$ComaCode = ',';
						$ComaBid = '';
						foreach($BannerData as $Banner){
							$BidderCode = $Banner['BidderCode'];
							$ZoneId = $Banner['ZoneId'];
							$PlacementParam = $Banner['PlacementParam'];
							$Code .= "$ComaBid{bidder: '$BidderCode', params: {"; 
							if($Banner['idBidderParam'] != '' && $Banner['idBidder'] != ''){
								$idBidderParam = $Banner['idBidderParam'];
								$idBidderC = $Banner['idBidder'];
								
								if($BidderCode == 'smartadserver'){
									$Code .= "$idBidderParam: $idBidderC ,";
								}else{
									$Code .= "$idBidderParam: '$idBidderC' ,";
								}
							}
							
							if($BidderCode == 'smartadserver'){
								$Code .= " $PlacementParam: $ZoneId ";
							}else{
								$Code .= " $PlacementParam: '$ZoneId' ";
							}
							
							if($BidderCode == 'aol' || $BidderCode == 'criteo'){
								$Banner['Floor'] = 0.8;
							}
							if($Banner['FloorParam'] != '' && $Banner['Floor'] > 0){
								$Floor = $Banner['Floor'];
								$FloorParam = $Banner['FloorParam'];
								$Code .= ", $FloorParam: $Floor ";
							}
							if($BidderCode == 'aol'){
								$Code .= ", hb_deal_onedisplay: 'MP-1-11303-1-WHBD2' ";
							}elseif($BidderCode == 'smartadserver'){
								$Code .= ", formatId: 42581, tagId: 'sas_42581'";
							}
							$Code .= "} }";
							$ComaBid = ',';
						}
						$Code .= "]}";
					}
					$Code .= "];";
					$Code .= "adUnits.push(adUnit);";
					$Code .= "Floors.push($Floor);";
				}
				
				$Code .= "console.dir(adUnits);\n";
				
				if(count($AdCodes) > 0){
					
					$Code .= "console.dir('Prebid loaded 2');\n";
					$Code .= "var pbjs = pbjs || {}; pbjs.que = pbjs.que || [];";
					$Code .= "\n\n function loadBanners$Platform(adUN) { ";
					 
					
					$Code .= "pbjs.que.push(function() { pbjs.addAdUnits(adUnits[adUN]);
					pbjs.setConfig({
					  userSync: {
					    filterSettings: {
					      iframe: {
					        bidders: '*',
					        filter: 'include'
					      }
					    }
					  }
					});
					pbjs.requestBids({
		            timeout: VIDOOMY_DISPLAY_TIMEOUT,
		            bidsBackHandler: function() {
		                var iframe = top.document.getElementById('postbid_iframe');
		                iframe.style = 'position:absolute;';
		                var iframeDoc = iframe.contentWindow.document;
						
						var bidsData = []; var bids = []; var responsesVidoo = []; var winner = 0; var hb_adid = 0; var hb_size = 0; var hb_height = 0; var hb_width = 0; var hb_cpm = 0; var comp_cpm = 0; ";
					
					foreach($AdCodes as $AdCode){
		                $Code .= "responsesVidoo.push(pbjs.getBidResponsesForAdUnitCode('$AdCode')); ";
		            }
		                
		            //$Code .= "console.dir(responsesVidoo); ";
		                
		            $Code .= "responsesVidoo.forEach(a => {
			                a.bids.forEach(o => {
				                if(o.currency == 'EUR'){
									comp_cpm = o.cpm * 1.15;
								}else{
									comp_cpm = o.cpm;
								}
							    if(comp_cpm > hb_cpm){
								    hb_cpm = comp_cpm;
								    hb_adid = o.adserverTargeting.hb_adid;
								    hb_size = o.size;
								    hb_height = o.height;
								    hb_width = o.width;
							    }
							    
							    bids.push({cpm: o.cpm, currency: o.currency, bidder: o.bidderCode, width: o.width, height: o.height, auc: o.adUnitCode, hb_adid: o.adserverTargeting.hb_adid, ntry: vidTry});
							});
		                }); 
		                
						if(hb_cpm >= Floors[adUN]){
							winner = hb_adid;
						}else{
							winner = 0;
						}
						bidsData = {url: encodeURI(window.location.href), winner: winner, bids: bids, mobile: 0, idim: idImpresion, ntry: vidTry, idSite: $idSite }
						
						reportStats$Platform(bidsData, winner);
	
						vidTry = vidTry + 1;
						";
					$Code .= "
						if(hb_cpm >= Floors[adUN]){ if(checkVidooPlayer() === false) {
							preventPlayer();
							shown = 1; var canvasLoc; var canvasCo;
							pbjs.renderAd(iframeDoc, hb_adid); 
							setTimeout(function(){ var body = top.document.getElementsByTagName('body')[0]; body.removeChild(top.document.getElementById('divToDis')); }, 20000); ";
							
						if($Platform == 1){
							
					$Code .= " if(hb_height <= 90){
								canvasCo = Math.round(hb_width / 2);
								canvasLoc = ' bottom: 0px; left:50%; margin-left:-' + canvasCo + 'px';
							}else{
								canvasLoc = ' right: 20px; bottom:20px;';
								top.document.getElementById('closeDV').style = 'float:left; margin-left:-20px; display:inline-block; width:20px; height:20px; background-color:#EDEDED; z-index: 9999; position:absolute;';
							}
							
							top.document.getElementById('divToDis').style = 'width: ' + hb_width + 'px; height: ' + hb_height + 'px; z-index: 9998; position: fixed;  display: inline-block;' + canvasLoc; ";
					
						}else{
					$Code .= " canvasCo = Math.round(hb_width / 2);
							canvasLoc = ' bottom: 0px; left:50%; margin-left:-'+canvasCo+'px';
													
							top.document.getElementById('divToDis').style = 'width: ' + hb_width + 'px; height: ' + hb_height + 'px; z-index: 9998; position: fixed;  display: inline-block;' + canvasLoc;	";
						}
					
					$Code .= " } }	
		            }
		        });
		    });
		    }";
				
				
				$Code .= " document.addEventListener('DOMContentLoaded', function(){ 
		var body = top.document.getElementsByTagName('body')[0];
		
		var ifr = document.createElement('iframe');
		ifr.id = 'postbid_iframe';
		ifr.frameBorder = '0';
		ifr.scrolling = '0';
		ifr.marginHeight = '0';
		ifr.marginWidth = '0';
		ifr.topMargin = '0';
		ifr.leftMargin = '0';
		ifr.allowTransparency = 'true';
		
		var closeBt = document.createElement('a');
		closeBt.href = \"javascript: var body = top.document.getElementsByTagName('body')[0]; body.removeChild(top.document.getElementById('divToDis'));\";
		closeBt.id = 'closeDV';
		var closeIm  = document.createElement('img');
		closeIm.id = 'closeDVi';
		closeIm.src = \"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAARCAQAAAB6UzRyAAAAAmJLR0QA/4ePzL8AAAAJcEhZcwAAAEgAAABIAEbJaz4AAACGSURBVCjPvdKxEcIgFIDhPzkn0RFY4TGCjqClZVaIpS2rPFZgBVfICFqYPEmCl1RQwfEd/NzRvNke7Q5TGx1+06cDki2FdB/WJ/UoYkS5lq67kEYmKIFHCQ14IkqHErj9D/ckemJO1khwvBBrK6Bvy4mYPWGBxFr8nOXomOV6Iudpo6n7Cz4azSC5YmnycgAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAxOS0wMy0wN1QwNTo1OTowMy0wNTowMMq/XvoAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMTktMDMtMDdUMDU6NTk6MDMtMDU6MDC74uZGAAAAAElFTkSuQmCC\";
		closeIm.style = 'width:16px; height:16px; margin-left: 2px; margin-top:1px;';
		closeBt.style = 'float:right; display:inline-block; width:20px; height:20px; top:0px; right:-20px; background-color:#EDEDED; z-index: 9999; position:absolute;';
		closeBt.appendChild(closeIm);
		
		var divtoD = document.createElement('div');
		divtoD.id = 'divToDis';
		divtoD.style = 'display:none;';
		
		divtoD.appendChild(ifr);
		divtoD.appendChild(closeBt);
		body.appendChild(divtoD);
		
		doDis = true;
		setTimeout(function(){ if(checkVidooPlayer()) { doDis = false; } if(doDis === true){ loadBanners$Platform(0); } }, 4000);
	}, false);";
				
				
				
				}
				
				
				if($Platform == 1){	
					$Code .= "\n\n//DISPLAY DESKTOP ENDS";
				}else{
					$Code .= "\n\n//DISPLAY MW ENDS";
				}
			}
			$NewCode .= $Code;
		}
		
		if($EricA == 3){
			$NewCode = '';
		}
		echo $Filename . "\n";
		$myfile = fopen("/var/www/html/ads/$Filename", "w") or die("Unable to open file!");
		fwrite($myfile, $NewCode);
		fclose($myfile);
		
		//echo $NewCode;
		
		chmod("/var/www/html/ads/$Filename", 0777);
		
		$mem_var = new Memcached();
		$mem_var->addServer("localhost", 11211);
		
		$mem_var->set("/$Filename", $NewCode, 21600);
	}else{
		$sql = "SELECT filename, eric, test FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
		$querySite = $db->query($sql);
		$SiteData = $db->fetch_array($querySite);
		
		$Filename = $SiteData['filename'];
		
		$arFilename = explode('/',$Filename);
		if(array_key_exists(3, $arFilename)){
			$Filename = $arFilename[3];
		
		
			$myfile = fopen("/var/www/html/ads/$Filename", "w") or die("Unable to open file!");
			fwrite($myfile, '');
			fclose($myfile);
		}
	}
	//return $NewCode;
}










function newGenerateJSOwnP($idSite){
	global $db;
	
	$sql = "SELECT " . USERS . ".LKQD_id FROM " . USERS . " INNER JOIN " . SITES . " ON " . SITES . ".idUser = " . USERS . ".id WHERE " . SITES . ".id = '$idSite'";
	$LKQDid = $db->getOne($sql);
	
	$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = '$idSite' AND Type = 3";
	$Desktop = $db->getOne($sql);
	$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = '$idSite' AND Type = 4";
	$Mobile = $db->getOne($sql);
	
	if($Desktop > 0 && $Mobile > 0){
		$Type = 10;
		echo '- BOTH ';
	}elseif($Desktop > 0){
		$Type = 11;
		echo '- ONLY DESKTOP ';
	}elseif($Mobile > 0){
		$Type = 12;
		echo '- ONLY MOBILE ';
	}else{
		$Type = 0;
		echo '- NONE ';
	}
	
	if($Type > 0){
		$sql = "SELECT Code FROM " . ADSCODES . " WHERE Type = '$Type' LIMIT 1";
		$Code = $db->getOne($sql);
		
		$sql = "SELECT * FROM " . ADS . " WHERE idSite = '$idSite' ORDER BY Type ASC";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			
			$sql = "SELECT filename FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
			$querySite = $db->query($sql);
			$SiteData = $db->fetch_array($querySite);
			
			$Filename = $SiteData['filename'];
			
			$arFilename = explode('/',$Filename);
			$Filename = $arFilename[3];
					
			while($Ads = $db->fetch_array($query)){
				$Type = $Ads['Type'];
				$idLKQD = $Ads['idLKQD'];
				
				$sql = "SELECT id FROM supplytag WHERE idTag = '$idLKQD' LIMIT 1";
				$ZoneID = $db->getOne($sql);
				
				$Code = str_replace('{{schain}}', '1.0,1!vidoomy.com,'.$LKQDid.',1,', $Code);
				
				if($Ads['SPosition'] == 1){ $SposTF = 'right'; } else { $SposTF = 'left'; }
				
				if($Ads['CloseTxt'] != ''){
					$Code = str_replace('{{closetxt}}',$Ads['CloseTxt'],$Code);
				}else{
					$Code = str_replace('{{closetxt}}', 'CLOSE',$Code);
				}
				
				if($Type == 3){
					$Code = str_replace('{{width}}',$Ads['Width'],$Code);
					$Code = str_replace('{{height}}',$Ads['Height'],$Code);
					$Code = str_replace('{{position}}',$SposTF,$Code);
					$Code = str_replace('{{zoneid}}',$ZoneID,$Code);
				}else{
					$Code = str_replace('{{widthM}}',$Ads['Width'],$Code);
					$Code = str_replace('{{heightM}}',$Ads['Height'],$Code);
					$Code = str_replace('{{positionM}}',$SposTF,$Code);
					$Code = str_replace('{{zoneidM}}',$ZoneID,$Code);	
				}
				/*
				if($Ads['Close'] == 1){ $CloseTF = 'true'; } else { $CloseTF = 'false'; }
				$Code = str_replace('#Close',$CloseTF,$Code);
				*/
			}
		}
		
		//echo $Filename;
		$myfile = fopen("/var/www/html/ads/newads/$Filename", "w") or die("Unable to open file!");
		fwrite($myfile, $Code);
		fclose($myfile);
		
		return true;
	}else{
		return false;
	}
}