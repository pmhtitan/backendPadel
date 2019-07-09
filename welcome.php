<?php
	// Initialize the session
	session_start();

	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
	 
	// Check if the user is logged in, if not then redirect him to login page
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
		header("location: index.php");
		exit;
	}
	// Include config file
	require_once "config.php"; /* $pdo para PDO consultas a la BBDD */
	// require_once "gestion.php";
	$pdo->query("SET NAMES 'utf8'");
	$stmtId = $pdo->query("SELECT * FROM Inscripciones ORDER BY id DESC");
	/* $stmtNoDuplicados = $pdo->query("SELECT DISTINCT * FROM Inscripciones ORDER BY id DESC");
	$InscripNoDuplicados = $stmtNoDuplicados->fetchAll(); */
	$InscripId = $stmtId->fetchAll(); 
	$q = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='Inscripciones'");

	$cadenaHead = array();
	/* Checkbox filtros headers por columnas */
	$valoresChecks = array(); /* ["foo","bar",] */

?>
 
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Panel de Control IPE</title>
		<!-- Works Online -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="assets/css/styles.css">
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<!-- Works Offline -->
		<!-- <link rel="stylesheet" type="text/css" href="bootstrap-4.3.1/css/bootstrap.min.css"> -->
		<!-- <link rel="stylesheet" type="text/css" href="DataTablesBootstrap4/datatables.min.css"/>
		<link rel="stylesheet" type="text/css" href="DataTablesBootstrap4/DataTables-1.10.18/css/dataTables.bootstrap4.min.css"/>
		<link rel="stylesheet" type="text/css" href="DataTablesBootstrap4/DataTables-1.10.18/css/jquery.dataTables.min.css"/>
		<link rel="stylesheet" type="text/css" href="DataTablesBoots4RespFill/Responsive-2.2.2/css/responsive.dataTables.min.css"/>
		<link rel="stylesheet" type="text/css" href="DataTablesBoots4RespFill/Responsive-2.2.2/css/responsive.bootstrap4.min.css"/> -->
		
		<!-- Works Online -->
		<!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
		<script src="/paneldecontrol/assets/jquery/jquery-3.4.1.min.js"></script>
		 <!-- <script src="https://code.jquery.com/jquery-3.4.1.js"></script> -->

		<!-- <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> <|<|<|Is working|>|>|>-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/jquery.dataTables.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/js/dataTables.bootstrap4.min.js"></script>
	  
		

		 <!-- Works Offline -->
		<!-- <script src="bootstrap-4.3.1/js/bootstrap.min.js"></script>
		<script src="DataTablesBootstrap4/datatables.min.js"></script>
		<script src="DataTablesBootstrap4/DataTables-1.10.18/js/dataTables.bootstrap4.min.js"></script>
		<script src="DataTablesBootstrap4/DataTables-1.10.18/js/jquery.dataTables.min.js"></script>
		<script src="DataTablesBoots4RespFill/Responsive-2.2.2/js/dataTables.responsive.min.js"></script>
		<script src="DataTablesBoots4RespFill/Responsive-2.2.2/js/responsive.bootstrap4.min.js"></script> -->
		<!-- <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>-->
	
		<script>
				var valoresTotal2 = [];
				var valoresTotal = {};
				var valoresFiltro = {};
				var valoresCheckHeaders = [];
				var valoresCheckSelect = [];
				var headers_de_selects = [];
				var header_select_multiple = [];
				$(document).ready(function(){
					
					var politica = "";
					var notifica ="";
					$('#tabladatos').DataTable();  
					
					$('input[type=checkbox]').on('change', function () {
						var len = $('input[type=checkbox]:checked').length;
						if (len > 0) {
							$('.mostrExportar2').removeClass('ocultar');
							$('.mostrExportar2').addClass('mostrar');
						} else if (len === 0) {
							$('.mostrExportar2').removeClass('mostrar');
							$('.mostrExportar2').addClass('ocultar');
						}
					 });
				

					$("#botonMostrar2").click(function(){
						if ($("input[type='checkbox']").is(':checked')) {
							$.each($("input[type='checkbox']:checked"), function(){ 
								valoresFiltro[$(this).val()] ="";
							});
						console.log("añado step 1");
						
						}
						if ($('input:checkbox[id=Sexo]:checked').is(':checked')) {
							valoresFiltro['Sexo'] =$("select[name=selSexo] option:selected").val(); 
							console.log("añado step 2");
						}
						if ($('input:checkbox[id=Politica]:checked').is(':checked')) {
							valoresFiltro['Politica'] =$("select[name=selPolitica]").val(); 
							console.log("añado step 3");
						}
						if ($('input:checkbox[id=Notificaciones]:checked').is(':checked')) {
							valoresFiltro['Notificaciones'] =$("select[name=selNotificaciones]").val();
							console.log("añado step 4");
						}
						if ($('input:checkbox[id=Confirmado]:checked').is(':checked')) {
							valoresFiltro['Confirmado'] =$("select[name=selConfirmado]").val();
							console.log("añado step 5");
						}
						if ($('input:checkbox[id=Sede]:checked').is(':checked')) {
							var arraySedesFiltro = [];   
								$('select[name="list_sedes[]"] option:selected').each(function() {
									arraySedesFiltro.push($(this).val());
									console.log("añado step 6");
								});
							valoresFiltro['Sede'] = arraySedesFiltro;
						}


						console.log("Click boton 2");
						if(valoresFiltro.length !== 0){
							 console.log("me llamo earl");
							 console.log(valoresFiltro);
							var jsonValoresTotal2 = JSON.stringify(valoresFiltro);
							$.ajax({
								 type:"POST",
								 url: "gestion.php",
								 data: {valTotal2:jsonValoresTotal2},
								 /*success: function(data)
								 {
									console.log(jsonValoresTotal2);
									console.log("valTotal2 POST recibido.");
								 }*/

							 })
							.done( function(response) {
                                console.log(response);
								if(response.success){
									console.log(response.data.tablafiltros);
									$('.mostrarParticipantesFiltrados #tabladatos2').html(response.data.tablafiltros);
									$('#tabladatos2').DataTable();
									$('html, body').animate({'scrollTop': $('#mostrarParticipantesFiltrados').offset().top}, 'slow', 'swing');
								}else{
									console.log("error al leer");
								}

							})
							.fail( function() {
								alert( 'Error!!' );						 
							});
							
						}
						
					});
					
					$(".enviarDatos").click(function(){
						if ($("input[type='checkbox']").is(':checked')) {
							$.each($("input[type='checkbox']:checked"), function(){ 
								valoresCheckHeaders.push($(this).val());
								valoresTotal2.push($(this).val());    
								valoresTotal[$(this).val()] ="";           
								console.log("push Headers");
							}); 
						   
							console.log(valoresTotal);
							console.log(JSON.stringify(valoresTotal));
						   
						    /* alert("Campos Seleccionados: " + valoresCheckHeaders.join(", ")); */
							console.log("Campos Seleccionados: " + valoresCheckHeaders); 
						}

						 /* code de pasar arrays a export goes here */
						 /*    $('input[class=selectSexo]').val */
						 /*    $('input[class=selectPolitica]').val */
						 /*    $('input[class=selectNotificaciones]').val */

					 

						/* $("input.selectSexo[type='checkbox']").is(':checked') */
						if ($('input:checkbox[id=Sexo]:checked').is(':checked')) {
							valoresCheckSelect.push($("select[name=selSexo]").val());
							headers_de_selects.push($("input:checkbox[id=Sexo]").val());
							valoresTotal['Sexo'] =$("select[name=selSexo] option:selected").val(); 
							console.log("pasa por sexo");
						//location.reload();
						}
						if ($('input:checkbox[id=Politica]:checked').is(':checked')) {
							valoresCheckSelect.push($("select[name=selPolitica]").val());
							headers_de_selects.push($("input:checkbox[id=Politica]").val());
							valoresTotal['Politica'] =$("select[name=selPolitica]").val();                    
							console.log("pasa por politica");
							//location.reload();
						}
						if ($('input:checkbox[id=Notificaciones]:checked').is(':checked')) {
							valoresCheckSelect.push($("select[name=selNotificaciones]").val());
							headers_de_selects.push($("input:checkbox[id=Notificaciones]").val());
							valoresTotal['Notificaciones'] =$("select[name=selNotificaciones]").val();                             
							console.log("pasa por por notificaciones");
							//location.reload();
						}
						if ($('input:checkbox[id=Confirmado]:checked').is(':checked')) {
							valoresCheckSelect.push($("select[name=selConfirmado]").val());
							headers_de_selects.push($("input:checkbox[id=Confirmado]").val());
							valoresTotal['Confirmado'] =$("select[name=selConfirmado]").val();         
							console.log("pasa por por confirmado");
							//location.reload();
						}
						if ($('input:checkbox[id=Sede]:checked').is(':checked')) { 
							var arraySedes = [];   
							$('select[name="list_sedes[]"] option:selected').each(function() {
								valoresCheckSelect.push($(this).val());
								arraySedes.push($(this).val());
							});
							valoresTotal['Sede'] = arraySedes;
							/*  var valoresSedes= JSON.stringify(arraySedes);
							console.log(valoresSedes); */
							header_select_multiple.push($("input:checkbox[id=Sede]").val());
						}
						// location.reload();
						console.log("Select rellenados: " + valoresCheckSelect);
						console.log("Headers select normales: " + headers_de_selects);
						console.log("Header Select multiple: " + header_select_multiple);
				 
						pasarChecks();
						// console.log($('#var1').val());
						// $("#formDatos").submit();
					 
					 
				

						$("select.politic").change(function(){
							var selectedPolitica = $("select.politic").children("option:selected").val();
							politica = $(this).val();
							console.log(politica);
						});
						$("select.notific").change(function(){
							var selectedNotifica = $("select.notific").children("option:selected").val();
							notifica = $(this).val();
							console.log(notifica);
						});
						$('html, body').animate({scrollTop:0}, 'slow');

					});
					/*  var selectedPolitica = $("select.politic").children("option:selected").val();
					 var selectedNotifica = $("select.notific").children("option:selected").val();
					 valoresCheckSelect.push(selectedPolitica); 
					  $(".enviarDatos").click(function(){
						  var convertido = selectedPolitica.toString();
					  alert("Push array: " + convertido);  
					}); 
					*/

					/* input[value="Sedes"] */
					$('#Sede').change(function()  {
						if( $('#Sede').is(':checked') ){
							$('.selectSedes').removeClass('ocultar');
							$('.selectSedes').addClass('mostrar');
						}else{
							$('.selectSedes').removeClass('mostrar');
							$('.selectSedes').addClass('ocultar');
						}
					});

					$('.ocultTblParticp').click(function(){
					/* 	$('.mostrarParticipantes').removeClass('mostrar');
						$('.mostrarParticipantes').addClass('ocultar'); */
						$('.mostrarParticipantes').fadeOut(500);
						/* $('#botonMostrar1').removeClass('ocultar');
						$('#botonMostrar1').addClass('mostrar'); */
						$('#botonMostrar1').fadeIn(500);
					});

					$('.divDescargar').click(function(){
						$('.divDescargar').fadeOut(500);
						});
					

					$("#Sede").one( "click", function() { 
						$.ajax({url: "welcome.php"
						})
							.done(function(){
							$(".selectSedes").html("<div>Sede/s</div><select name='list_sedes[]' class='targetSede' multiple><?php $stmtNoDuplicados = $pdo->query('SELECT DISTINCT(Sede) FROM Inscripciones ORDER BY Sede ASC'); $InscripNoDuplicados = $stmtNoDuplicados->fetchAll(); foreach($InscripNoDuplicados as $options) {?><option value='<?php echo $options['Sede'] ?>'><?php echo $options['Sede'] ?></option><?php } ?> </select></div></div>");
							});
						
					});

			$('#Apellido').change(function()  {
				if( $('#Apellido').is(':checked') ){
					$('.selectApellido').removeClass('ocultar');
					$('.selectApellido').addClass('mostrar');
				}else{
					$('.selectApellido').removeClass('mostrar');
					$('.selectApellido').addClass('ocultar');
				   
				}
			});
			$('#Ciudad').change(function()  {
				if( $('#Ciudad').is(':checked') ){
					$('.selectCiudad').removeClass('ocultar');
					$('.selectCiudad').addClass('mostrar');
				}else{
					$('.selectCiudad').removeClass('mostrar');
					$('.selectCiudad').addClass('ocultar');
				   
				}
			});
			$('#Sexo').change(function()  {
				if( $('#Sexo').is(':checked') ){
					$('.selectSexo').removeClass('ocultar');
					$('.selectSexo').addClass('mostrar');
				}else{
					$('.selectSexo').removeClass('mostrar');
					$('.selectSexo').addClass('ocultar');
				   
				}
			});
			$('#ConoCircuito').change(function()  {
				if( $('#ConoCircuito').is(':checked') ){
					$('.selectConoCircuito').removeClass('ocultar');
					$('.selectConoCircuito').addClass('mostrar');
				}else{
					$('.selectConoCircuito').removeClass('mostrar');
					$('.selectConoCircuito').addClass('ocultar');
				   
				}
			});
			$('#Rol').change(function()  {
				if( $('#Rol').is(':checked') ){
					$('.selectRol').removeClass('ocultar');
					$('.selectRol').addClass('mostrar');
				}else{
					$('.selectRol').removeClass('mostrar');
					$('.selectRol').addClass('ocultar');
				   
				}
			});
			$('#Politica').change(function()  {
				if( $('#Politica').is(':checked') ){
					politica = $("select.politic").val();
					console.log(politica);
					$('.selectPolitica').removeClass('ocultar');
					$('.selectPolitica').addClass('mostrar');
				}else{
					$('.selectPolitica').removeClass('mostrar');
					$('.selectPolitica').addClass('ocultar');
					politica = "";
				   
				}
			});
			$('#Notificaciones').change(function()  {
				if( $('#Notificaciones').is(':checked') ){
					notifica = $('select.notific').val();
					console.log(notifica);
					$('.selectNotificaciones').removeClass('ocultar');
					$('.selectNotificaciones').addClass('mostrar');
				}else{
					$('.selectNotificaciones').removeClass('mostrar');
					$('.selectNotificaciones').addClass('ocultar');
				   
				}
			});
			$('#Confirmado').change(function()  {
				if( $('#Confirmado').is(':checked') ){
					$('.selectConfirmado').removeClass('ocultar');
					$('.selectConfirmado').addClass('mostrar');
				}else{
					$('.selectConfirmado').removeClass('mostrar');
					$('.selectConfirmado').addClass('ocultar');
				   
				}
			});

				$('.mostrExportar1').click(function() {
				/* 	$('.mostrExportar1').addClass('ocultar');
					$('.mostrExportar1').removeClass('mostrar');
					$('.mostrarParticipantes').addClass('mostrar');
					$('.mostrarParticipantes').removeClass('ocultar'); */
					$('.mostrExportar1').fadeOut(200);
					$('.mostrarParticipantes').fadeIn(500);
					
					
				});
				$('.mostrExportar2').click(function() {
					$('.mostrExportar2').addClass('ocultar');
					$('.mostrExportar2').removeClass('mostrar');
					$('.mostrarParticipantesFiltrados').addClass('mostrar');
					$('.mostrarParticipantesFiltrados').removeClass('ocultar');
					
				});
			});
		</script>

	<script>
	  
		function snackbarStart() {
			var x = document.getElementById("snackbar");
			x.className = "show";
			setTimeout(function(){ x.className = x.className.replace("show", ""); }, 4000);
		}

		function pasarChecks(){
			<?php  $filenam = 'filtros/Inscripciones_Seleccionadas_' . date('Y-m-d-H') . '.csv'; ?>
			if(valoresCheckHeaders.length > 0){
				console.log(valoresTotal);
			// var replacer = function(k, v) { if (v === undefined) { return null; } return v; };
				var datoProbador = "soy dato Probador, he conseguido llegar a exportFiltro correctamente.";
				var jsonValoresTotal = JSON.stringify(valoresTotal);
			// var urlExport ="exportFiltro.php";
				console.log(jsonValoresTotal);
					$.ajax({
							method: "POST",
							url: "exportFiltro.php",
							data: {valTotal:jsonValoresTotal, datoP:datoProbador}
						})
						.done(function(response){
								console.log("AJAX. Todo correcto compadre");
								$.ajax({
									url: "welcome.php"
								})
									.done(function(response2){
										$(".divDescargar").html("<a href=" + '<?php echo $filenam?>' + " class='btn btn-primary botonDescargar' id='botonDescargar'>Descargar CSV</a>").fadeIn(500);
										snackbarStart();
									//    window.location = urlReload;	
									});
						})
						.fail(function(jqXHR, status, error){
							alert('Hubo un problema con el AJAX de pasarChecks');
							console.log("Error en: " + jqXHR.status + jqXHR.statusText +status + "error: " + error);
						})
						.always(function(){
							console.log('Petición ajax realizada.');
						});
				
					
			}else{
				alert("Para exportar 'A la carta' tienes que filtrar por al menos un campo.");
			}
		 }
		 
		/*function comprobarExport(){
		
		} */
		 

			   
	</script>

	</head>
	<body>
		<div class="page-header">
			<h1>Hola, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bienvenido a panel de control IPE.</h1>
		</div>
		<p>
			<a href="reset-password.php" class="btn btn-warning">Quiero cambiar la Contraseña</a>
			<a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
		</p>
		<div>
			<input type="button" id="botonMostrar1" class="btn btn-primary pull-center mostrExportar1 botonParticipantes" value="Mostrar Participantes"/>
			<input type="button" id="botonMostrar2" class="btn btn-info pull-center mostrExportar2" value="Mostrar participantes filtrados">
			<a href="export.php" class="btn btn-success pull-center mostrExportar exportarTodo1">Exportar TODO a CSV</a>
			<!-- <a href="exportFiltro.php" class="btn btn-success pull-right"> -->
			<input type="button" class="btn btn-success pull-right enviarDatos" value="Exportar 'a la carta' CSV">
			<div class="divDescargar pull-right"></div>
			<!-- </a> -->
		</div>
		<!-- <form style="display: hidden" action="exportFiltro.php" method="POST" id="formDatos">
			<input type="hidden" id="var1" name="var1" value=""/>
		</form> -->
		<div class="divCheckboxes">
			<?php
				$row = $q->fetch(PDO::FETCH_ASSOC);
				//array_push($cadenaHead, $row['column_name']);
				//echo json_encode($row);
				$cadenaHead = array();
			 ?>
				<div><?php //print_r($cadenaHead);?></div>
			 <?php
				$cadena = "<div class='styleChecks'>";
				while($row = $q->fetch(PDO::FETCH_ASSOC)){
					$cadena .= "<input type='checkbox' value='".$row['column_name']."' id='".$row['column_name']."'>" . "<label for='".$row['column_name']."'>".$row['column_name'] .'</label>' . ' ' ;
					array_push($cadenaHead, $row['column_name']); 
					//foreach ($ as $value) {
				}
			   $cadena .= "</div>";
			   echo $cadena;

			?>
		<div class="marginTopSelección"></div>

		    <!--  <div class="selectApellido">
			<div>Apellido</div>
			<select name="list_apellido[]" multiple>
				<?php
					$stmtNoDuplicados = $pdo->query("SELECT DISTINCT(Apellido) FROM Inscripciones ORDER BY Apellido ASC");
					$InscripNoDuplicados = $stmtNoDuplicados->fetchAll();
					foreach($InscripNoDuplicados as $options) {
				?>
				<option value="<?php echo $options['Apellido'] ?>"><?php echo $options['Apellido'] ?></option>

				<?php } ?> 
				</select>
			  </div> -->
			

			
		   <!--  <div class="selectCiudad">
				<div>Ciudad</div>
			<select name="list_ciudad[]" multiple>
				<?php
					/*   for(int i=0; i < ) */
					$stmtNoDuplicados = $pdo->query("SELECT DISTINCT(Ciudad) FROM Inscripciones ORDER BY Ciudad ASC");
					$InscripNoDuplicados = $stmtNoDuplicados->fetchAll();
					foreach($InscripNoDuplicados as $options) {
				?>
						<option value="<?php echo $options['Ciudad'] ?>"><?php echo $options['Ciudad'] ?></option>

					<?php } ?>
				</select>
			</div> -->

			
			<div class="selectSexo">
				<div>Sexo</div>
				<select name="selSexo" class="targetSexo">
					<option selected value> -- Todos -- </option>
					<option value="F">F</option>
					<option value="M">M</option>
				</select>
			</div>
			<!-- <div class="selectConoCircuito">
			<div>Cono Circuito</div>
				<select>
					<option value="SI">SI</option>
					<option value="NO">NO</option>
				</select>
			</div> -->
	   <!-- <div class="selectRol">
				<div>Rol</div>
				<select>
					<option value="JUGADOR">JUGADOR</option>
					<option value="ESPECTADOR">ESPECTADOR</option>
				</select>
			</div> -->
			<div class="selectPolitica">
				<div>Politica</div>
				<select class="politic" name="selPolitica">
					<option selected value> -- Todos -- </option>
					<option value="SI">SI</option>
					<option value="NO">NO</option>
				</select>
			</div>
			<div class="selectNotificaciones">
				<div>Notificaciones</div>
				<select class="notific" name="selNotificaciones">
					<option selected value> -- Todos -- </option>
					<option value="SI">SI</option>
					<option value="NO">NO</option>
				</select>
			</div>
			<div class="selectConfirmado">
				<div>Confirmado</div>
				<select class="confirm" name="selConfirmado">
					<option selected value> -- Todos -- </option>
					<option value="Si">Si</option>
					<option value="No">No</option>
				</select>
			</div>

			
			<div class="selectSedes"></div>
		   
				
		</div>
			<div class="container mostrarParticipantes" id="mostrarParticipantes">
				<div class="panel panel-default">
					<div class="panel-heading panel1">
						<button type="button" class="btn btn-secondary pull-left ocultTblParticp">Ocultar tabla</button>
					-- Participantes --
						<a href="export.php" class="btn btn-success pull-right ocultExportar">Exportar TODO a CSV</a>
					</div>
					<div class="panel-body">
						<table id="tabladatos" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Apellido</th>
									<th>Email</th>
									<th>Teléfono</th>
									<th>Ciudad</th>
									<th>Fecha Nacimiento</th>
									<th>Sexo</th>
									<th>Rol</th>
									<th>SEDE</th>
						  
						 
								</tr>
							</thead>
							<tbody>
							<?php   
					   
								// if($InscripId->num_rows > 0){ 
								// while($rows = $InscripId->fetch_assoc()){
								foreach($InscripId as $rows) {  ?>                
									<tr><!-- idea para filtrar cada row por el contenido, meter lo mismo que los headers y llamarlos igual. $rows[header1]; -->
										<td><?php echo $rows['Nombre']; ?></td>
										<td><?php echo $rows['Apellido']; ?></td>
										<td><?php echo $rows['Email']; ?></td>
										<td><?php echo $rows['Telefono']; ?></td>
										<td><?php echo $rows['Ciudad']; ?></td>
										<td><?php echo $rows['FechaNacimiento']; ?></td>
										<td><?php echo $rows['Sexo']; ?></td>
										<td><?php echo $rows['Rol']; ?></td> 
										<td><?php echo $rows['Sede']; ?></td>
									</tr>
								<!--?php  }  }else{ ?-->
								<!--tr><td colspan="5">No se han encontrado participantes.....</td></tr-->
							<?php } ?>
							</tbody>
						</table>
					</div> 
				</div>
			</div>
			<div class="container mostrarParticipantesFiltrados" id="mostrarParticipantesFiltrados">
				<div class="panel panel-default">
					<div class="panel-heading panel2">
					-- Participantes Filtrados --
					<input type="button" class="btn btn-success pull-right enviarDatos" value="Exportar 'a la carta' CSV">
				</div>
			<div class="panel-body">
				<table id="tabladatos2" class="table table-striped table-bordered">
					
						<thead id="theadDatos2">               
							
						</thead>
						<tbody>
							
						</tbody>
						
				</table>
			</div>

			<div id="snackbar">Su CSV personalizado está listo, puedes descargarle pulsando en 'Descargar CSV' azul.</div>
	</body>
</html>




