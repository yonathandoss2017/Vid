		<!--<hdcn>-->
		<header class="hdcn">
			<div class="cnt a-fx">
                                
				<div class="logo">
                    <div class="on-user dropdown drp-lf hidden visible-xs">
						<a href="index.php" data-toggle="dropdown">
							<!--<img src="img/cnt/user.jpg" alt="user">-->
							<i class="material-icons menu-hd">menu</i>
						</a>			
					</div>        
                    <a href="#"><img src="img/vidoomy-logo.png" class="mh-logo" alt="vidoomy"></a>
                </div>
				
				<nav class="mn-user a-flx1">
					<ul class="0-fx">
						<li class="nt-user 0-fx 0-flx1">
                            <!--<div class="mj-user dropdown drp-lf">
								<a href="#" class="fa-envelope-o mat-email"><span>Account Manager asignado <span>Marcos Cuesta</span></span><i class="lb-num">3</i></a>
								<div class="dropdown-menu">
									<div class="bx-cn">
										<div class="bx-hd dfl 0-fx">
											<span>Tienes 56 Avisos</span>
											<span class="0-rt">Caracteres <small>0/400</small></span>
										</div>
										<div class="bx-bd">
											<form action="#" class="bx-pd frm-sndmj">
												<div class="frm-group">
													<textarea cols="66" rows="6" placeholder="Escrtibe aqui tu mensaje"></textarea>
												</div>
												<div class="dfl b-fx">
													<div class="frm-group b-flx1">
														<input type="file" class="filestyle" data-input="false" data-icon="false" data-buttonName="uplfil" data-buttonText="Adjuntar archivo <span class='fa-file-pdf-o'>.pdf</span> o <span class='fa-file-picture-o'>.jpg</span>">
													</div>
													<div class="frm-group b-rt">
														<button type="submit">Enviar</button>
													</div>
												</div>												
											</form>
										</div>
									</div>
								</div>
							</div>
							<div class="dropdown drp-lf">
								<a href="#" class="fa-bell-o mat-bell bt-dpdw" data-toggle="dropdown"><i class="lb-num-b">3</i></a>
								<div class="dropdown-menu">
									<div class="bx-cn">
										<div class="bx-hd dfl b-fx">
											<span>Tienes 56 Avisos</span>
										</div>
										<div class="bx-bd">
											<ul class="lst-dpdw">
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li>
													<a href="#">Marcos Cuesta <span>Pago realizado con éxito</span> <small>5 min.</small><i class="material-icons">add_alarm</i></a>
												</li>
												<li class="dpdw-f"><a href="#">Ver todos los avisos</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>-->
                                                        
						</li>
						<li class="op-user 0-fx 0-rt hidden-xs menu-r"><?php
	                            if($_SESSION['Type'] == 1){
		                            ?><a href="index.php"><i class="material-icons">account_circle</i><span>Publishers</span></a><?php
			                    }elseif($_SESSION['Type'] == 3){
				                	?><a href="index.php"><i class="material-icons">account_circle</i><span>Publishers</span></a>
				                	<a href="advertisers.php"><i class="material-icons">account_circle</i><span>Anunciantes</span></a>
				                	<a href="bidders.php"><i class="material-icons">gavel</i><span>Bidders</span></a>
				                	<a href="ad-units.php"><i class="material-icons">aspect_ratio</i><span>Ad Units</span></a><?php	
				                }else{
				                    ?><a href="advertisers.php"><i class="material-icons">account_circle</i><span>Anunciantes</span></a><?php  
			                    }  
                            ?><!--<a href="#"><i class="material-icons">euro_symbol</i><span>Facturación</span></a>-->
                            <a href="stats.php"><i class="material-icons">pie_chart</i><span>Estadísticas</span></a><?php
	                            if($_SESSION['Type'] == 3 || $_SESSION['Type'] == 1){
		                            ?><a href="pages.php"><i class="material-icons">create_new_folder</i><span>Páginas</span></a><?php					
			                    }
	                            if($_SESSION['Type'] == 3){
		                            ?><a href="acc-managers.php"><i class="material-icons">vpn_lock</i><span>Acc Managers</span></a>
		                            <?php
	                            }
	                            if($_SESSION['Type'] == 1){
		                            ?><a href="pending.php"><i class="material-icons">assignment</i><span>Pendientes</span></a><?php
	                            }
                            ?>
                            
                            <a href="logout.php" class="logout"><i class="material-icons">power_settings_new</i><span>Desconectar</span></a>
						</li>
					</ul>
				</nav>
			</div>
		</header>
		<!--<hdcn>-->