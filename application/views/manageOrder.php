<!-- TABLE: LATEST ORDERS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
	#modalImage {
		align-self: center;
		min-width: 100px;
		min-height: 150px;
		max-width: 150px;
		max-height: 200px;
		/* height: auto; */
		/* margin: 10%; */


	}
</style>

<div class="box">

	<div class="box-header box-header-background with-border">

		<h3 class="box-title">Toutes les commandes

		</h3>

	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class="table-responsive">
			<?php if ($ordersTab != null) { ?>
				<table class="table no-margin">
					<thead>
						<tr>
							<th>N° commande</th>
							<th>Client</th>
							<th>Email</th>
							<th>Code postal</th>
							<th>Date</th>
							<!-- <th>Représentant</th>
							<th>Date de livraison prévue</th> -->
							<th>Montant</th>
							<th>Statut</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($ordersTab as $ordersTable) {


							echo ' <tr class="custom-tr">
                                            <td class="vertical-td">';

							echo $ordersTable['id'];
							if (!$ordersTable['ex']) {

								echo '<td class="vertical-td text-danger"align="center">client supprimé</td>';
								echo '<td class="vertical-td text-danger"align="center">-</td>';
								echo '<td class="vertical-td text-danger"align="center">-</td>';
							} else {
								echo '</td>';
								echo '<td class="vertical-td">' . $ordersTable['firstname'] . ' ' . $ordersTable['lastname'];
								echo '</td>
                                            		<td class="vertical-td">';
								echo $ordersTable['email'];
								echo '</td>';
								echo '<td class="vertical-td">' . $ordersTable['postcode'] . '</td>';
							}
							echo '
                                            <td class="vertical-td">';
							echo $ordersTable['date_add'];
							// echo '</td>                                           
                            //                 <td class="vertical-td">*';

							// echo '</td>
                            //                 <td class="vertical-td">';

							// echo $ordersTable['delivery_date'];
							echo '</td>
                                            <td class="vertical-td">';
							echo $ordersTable['total'].'MAD';
							echo '</td>
                                            <td class="vertical-td">';
											$colorClass = '';

							switch ($ordersTable['id_statut']) {
								case 6:
								case 8:
									$colorClass = ' bg-danger'; // Rouge
									break;
								case 2:
								case 5:
								case 7:
								case 11:
									$colorClass = ' bg-success'; // Vert
									break;
								default:
									$colorClass = 'bg-primary'; // Bleu par défaut
									break;
							}?>
											
									<label for="select" class="sr-only">Change Order Status</label>

									<form id="statusForm" action="<?= site_url('OrderController/modifierStatut') ?>" method="POST">
										<input type="hidden" id="order_id" name="order_id" value="<?= $ordersTable['id']?>">
										<input type="hidden" id="order_total" name="order_total" value="<?= $ordersTable['total']?>">
										<select class="status-select" id="select" name="new_status" disabled>
											<option value="1" <?= $ordersTable['id_statut'] == 1 ? 'selected' : '' ?> >En attente du paiement par chèque</option>
											<option value="2"<?= $ordersTable['id_statut'] == 2? 'selected' : '' ?>>Paiement accepté</option>
											<option value="3"<?= $ordersTable['id_statut'] == 3? 'selected' : '' ?>>En cours de préparation</option>
											<option value="4"<?= $ordersTable['id_statut'] ==  4? 'selected' : '' ?>>Expédié/option>
											<option value="5"<?=$ordersTable['id_statut'] == 5? 'selected' : '' ?>>Livré</option>
											<option value="6"<?=$ordersTable['id_statut'] == 6? 'selected' : '' ?>>Annulé</option>
											<option value="7"<?= $ordersTable['id_statut'] == 7 ? 'selected' : '' ?>>Remboursé</option>
											<option value="8"<?= $ordersTable['id_statut'] == 8 ? 'selected' : '' ?>>Erreur de paiement</option>
											<option value="9"<?= $ordersTable['id_statut'] == 9? 'selected' : '' ?>>En attente de réapprovisionnement (payé)</option>
											<option value="10"<?= $ordersTable['id_statut'] == 10 ? 'selected' : '' ?>>En attente de virement bancaire</option>
											<option value="11"<?= $ordersTable['id_statut'] ==  11? 'selected' : '' ?>>Paiement à distance accepté</option>
											<option value="12"<?= $ordersTable['id_statut'] == 12 ? 'selected' : '' ?>>En attente de réapprovisionnement (non payé)</option>
											<option value="13"<?= $ordersTable['id_statut'] == 13 ? 'selected' : '' ?>>En attente de paiement à la livraison</option>
											<option value="14"<?= $ordersTable['id_statut'] == 14? 'selected' : '' ?>>En attente de paiement</option>
											<option value="15"<?= $ordersTable['id_statut'] == 15 ? 'selected' : '' ?>>Remboursement partiel</option>
											<option value="16"<?= $ordersTable['id_statut'] == 16 ? 'selected' : '' ?>>Paiement partiel</option>
											<option value="17"<?= $ordersTable['id_statut'] == 17? 'selected' : '' ?>>Autorisation. A capturer par le marchand</option>
										</select>
									</form>
											<?php 
							
							echo '</td>';

						?>

							<td>
								<!-- <a href=" " class="btn bg-success btn-xs" title="Chercher" data-toggle="tooltip" data-placement="top" > -->
								<a href="#" data-toggle="modal" data-target="#maModal" data-options="<?php echo htmlspecialchars(json_encode($ordersTable['products']), ENT_QUOTES, 'UTF-8'); ?>" class="btnPasser btn bg-warning btn-xs" title="Chercher" data-title="<?php echo  $ordersTable['id']; ?>" data-status="<?php echo  $ordersTable['statut']; ?>" data-status-color="<?php echo $colorClass ?>" data-total="<?php echo  $ordersTable['total']; ?>">

									<i class="glyphicon glyphicon-search"></i>
								</a>
								<button class="change-statut btn btn-primary" ><i class="fa-solid fa-pen-to-square"></i></button>
								<button class="save-status btn btn-success" onclick="submitForm();" style="display: none;"><i class="fa-solid fa-square-check"></i></button>



							</td>


							</tr>
							
						<?php
						}
						?>

					</tbody>
				</table>

				
				<div class="modal fade" id="maModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="title"></h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body" style="align-items: center; text-align:center ">
								<div class="d-flex ">
									 <p id="id-commande"></p>
								</div>
								<div class="d-flex ">
								<p id="statut-commande" ></p>
								</div>
								<div class="d-flex ">
									 <p id="total-commande"></p>
								</div>
								<!-- <img id="modalImage" src="" alt="Product Image" style="width: 300px; "> -->
								<p id="modalContent" style="font-size: 13px;"></p>
							</div>
						</div>
					</div>
				</div>
			<?php
			} else echo '<div class="box-footer text-center">
								Aucune commande à afficher

								</div>';
			?>
		</div>
		<!-- /.table-responsive -->
	</div>


	<!-- /.box-footer -->
</div>
<!-- /.box -->


<!-- /.box -->
</div>
<!-- /.col -->

<!-- /.col -->
</div>
<script>
    function submitForm() {
        document.getElementById('statusForm').submit();
    }
</script>
<script>
$(document).ready(function() {
    $('.change-statut').on('click', function() {
        var row = $(this).closest('tr');
        row.find('.status-select').prop('disabled', false);
        row.find('.save-status').show();
        $(this).hide();
		// console.log('test');
    });

	
});


</script>
<script>
	// Sélectionner tous les boutons avec la classe btnPasser
	let btnsPasser = document.querySelectorAll(".btnPasser");

	// Ajouter un événement click à chaque bouton
	btnsPasser.forEach(function(btn) {
		btn.addEventListener("click", function() {
			var modal = document.getElementById("maModal");
			var title = document.getElementById("title");
			var modalContent = document.getElementById('modalContent');
			var order_statut = document.getElementById('statut-commande');
			var order_total = document.getElementById('total-commande');
			var order_id = document.getElementById('id-commande');
			modalContent.innerHTML = '';
			title.innerHTML = 'Details de la commande #';
			modal.style.display = "block";
			var table = document.createElement('table');
			table.classList.add('table', 'table-bordered', 'table-striped', 'table-position');
			var headerRow = table.insertRow();
			var headerCell1 = headerRow.insertCell();
			var headerCell2 = headerRow.insertCell();
			headerCell1.textContent = 'Nom du produit';
			headerCell2.textContent = 'Quantité';
			order_total.textContent = 'Total :'+btn.getAttribute("data-total")+' MAD';
			order_statut.textContent ='Statut: '+ btn.getAttribute("data-status");
			order_id.textContent ='N° Commande: '+ btn.getAttribute("data-title");
			let options = JSON.parse(btn.getAttribute('data-options'));
			let titre = btn.getAttribute("data-title");


			for (let index in options) {
				var rowData = table.insertRow();
				var cell1 = rowData.insertCell();
				var cell2 = rowData.insertCell();
				cell2.textContent = options[index];
				cell1.textContent = index;
			}

			title.append(titre)
			modalContent.appendChild(table);
		});
	});

	function fermerModal() {
		var modal = document.getElementById("maModal");
		modal.style.display = "none";
	}
</script>
<!-- /.row -->
</section>