<!-- TABLE: LATEST ORDERS -->

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


	<!-- /.box-header -->
	<div class="box box-primary">
		<div class="box-header box-header-background with-border">
			<h3 class="box-title">Touts les produits</h3>
			
		</div>
		<!-- /.box-header -->

		<div class="box-body">

			<?php if ($productsTab != null) { ?>
				<table class="table table-bordered table-striped" id="dataTables-example">
					<thead><!-- Table head -->
						<tr>
							<th class="col-sm-1 active" style="width: 21px"><input type="checkbox" class="checkbox-inline" id="parent_present" /></th>
							<th class="active">Ref #</th>
							<th class="active">Nom</th>
							<th class="active">Marque</th>
							<!-- <th class="active">Ligne</th> -->
							<!-- <th class="active">Coloris</th> -->
							<th class="active">Promo</th>
							<th class="active">Condition</th>
							<th class="active" style="text-align: center" style="width:30px">Quantité</th>
							<th class="active">Prix</th>
							<th class="active">Statut</th>
							<th class="active" style="width:100px;">Action</th>
						</tr>
					</thead>
					<tbody><!-- / Table body -->
						<?php {
							foreach ($productsTab as $prod) { ?>

								<tr class="custom-tr">
									<td class="vertical-td"><input name="product_id[]" value=" ?>" class="child_present" type="checkbox" /></td>
									<td class="vertical-td"><?php echo $prod['reference']; ?></td>
									<td class="vertical-td"><?php echo $prod['nom']; ?></td>
									<td class="vertical-td"><?php echo $prod['marque']; ?></td>
									<!-- <td class="vertical-td">*</td> -->
									<!-- <td class="vertical-td"><?php
																	if (is_array($prod['options'])) {


																		foreach ($prod['options'] as $option) {
																			print_r($option);
																		}
																	} else {
																		echo $prod['options'];
																	}

																	?></td> -->
									<td class="vertical-td"><?php echo $prod['promo']; ?></td>
									<td class="vertical-td"><?= $prod['condition'] ?></td>
									<td class="vertical-td">
										<?php {


											if ($prod['total_quantity'] > 0) {

												echo $prod['total_quantity'];
											} else {
												echo '<span class="label label-danger">RUPTURE</span>';
											}
										}

										?>
										<?php
										$tableau_php = $prod[$prod['id']]['options'];
										$tableau_image = $prod[$prod['id']]['option_images'];
										?>

									</td>
									<td class="vertical-td"><?= $prod['prix']; ?></td>
									<td class="vertical-td"><?php if (!$prod['statut']) {
																echo '<span class="label label-warning">Inactive</span>';
															} else {
																echo '<span class="label label-primary">Active</span>';
															}
															?></td>

									<td class="vertical-td">
										<a href="#" data-toggle="modal" data-target="#maModal" data-images="<?php echo htmlspecialchars(json_encode($prod[$prod['id']]['option_images']), ENT_QUOTES, 'UTF-8'); ?>" data-options="<?php echo htmlspecialchars(json_encode($prod[$prod['id']]['options']), ENT_QUOTES, 'UTF-8'); ?>" class="btnPasser btn bg-success btn-xs" title="Chercher" data-title="<?php echo $prod['nom']; ?>" data-image="<?php echo $prod['image_link']; ?>">
											<!-- <a href="#" onclick="afficherModal('<?php echo htmlspecialchars($jsonData, ENT_QUOTES, 'UTF-8'); ?>','<?= $prod['reference'] ?>','<?= $prod['image_link'] ?>')" class="btn bg-success btn-xs" title="Chercher" data-toggle="tooltip" data-placement="top"> -->
											<i class="glyphicon glyphicon-search"></i>
										</a>
									</td>

								</tr>
						<?php

							}
						}
						?>


					</tbody>
				</table>
				<div>
					<?= $links; ?>
				</div>

				<div class="modal fade" id="maModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="title">Details du produit:# </h4>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body" style="align-items: center; text-align:center ">
								<div class="d-flex justify-content-center">
									<img id="modalImage" src="" alt="Product Image" style="height: 100px; width:auto; margin:5%;">
								</div>
								<!-- <img id="modalImage" src="" alt="Product Image" style="width: 300px; "> -->
								<p id="modalContent" style="font-size: 13px;"></p>
							</div>
						</div>
					</div>
				</div>
			<?php
			} else echo '<div class="box-footer text-center">
									Aucun Produit à afficher
									</div>';
			?>

		</div>

	</div>
</div>
<script>
	// Sélectionner tous les boutons avec la classe btnPasser
	let btnsPasser = document.querySelectorAll(".btnPasser");

	// Ajouter un événement click à chaque bouton
	btnsPasser.forEach(function(btn) {
		btn.addEventListener("click", function() {
			var modal = document.getElementById("maModal");
			var title = document.getElementById("title");
			var modalImage = document.getElementById("modalImage");
			var modalContent = document.getElementById('modalContent');
			modalContent.innerHTML = '';
			title.innerHTML = 'Details du produit  #';
			// modal.style.display = "block";
			var table = document.createElement('table');
			var paragraph = document.createElement('p');
			paragraph.innerHTML='Produit simple';
			paragraph.style.fontSize = '18px';
			paragraph.style.fontFamily = 'Arial, sans-serif';
			paragraph.style.color = '#333';
			paragraph.style.lineHeight = '1.5';
			paragraph.style.padding = '10px';
			paragraph.style.backgroundColor = '#f0f0f0';
			paragraph.style.borderRadius = '5px';
			table.classList.add('table', 'table-bordered', 'table-striped', 'table-position');
			table.style.alignContent = 'center';
			var headerRow = table.insertRow();
			var headerCell1 = headerRow.insertCell();
			var headerCell2 = headerRow.insertCell();
			var headerCell3 = headerRow.insertCell();
			headerCell1.textContent = 'Option';
			headerCell2.textContent = 'Quantité';
			headerCell3.textContent = 'Image';
			headerCell1.style.textAlign = 'center';
			headerCell2.style.textAlign = 'center';
			headerCell3.style.textAlign = 'center';

			let options = JSON.parse(btn.getAttribute('data-options'));
			let images = JSON.parse(btn.getAttribute('data-images'));
			let titre = btn.getAttribute("data-title");
			let image = btn.getAttribute("data-image");
			modalImage.src = image;

			


			for (let index in options) {
				var rowData = table.insertRow();
				// rowData.style.alignContent='center';

				var cell1 = rowData.insertCell();
				var cell2 = rowData.insertCell();
				var cell3 = rowData.insertCell();
				cell2.textContent = options[index];
				cell1.textContent = index;
				cell1.style.textAlign = 'center';
				cell2.style.textAlign = 'center';
				cell3.style.textAlign = 'center';
				cell1.style.alignContent = 'center';
				cell2.style.alignContent = 'center';
				cell3.style.alignContent = 'center';


				// var link = document.createElement('a');
				var img = document.createElement('img');
				img.src = images[index];
				img.alt = "Image" // Optionally set an alt attribute
				img.style.width = 'auto'; // Optionally set the width
				img.style.height = '70px'; // Optionally set the height
				// img.style.margin='5%';

				// link.href = images[index];
				// link.textContent = images[index];
				cell3.appendChild(img);
				// cell3.textContent =images[index];
			}

			title.append(titre)

			if(options.length===0){
				modalContent.appendChild(paragraph);

			}else{
				modalContent.appendChild(table);
			}

		});
	});

	// function fermerModal() {
	// var modal = document.getElementById("maModal");
	// // modal.style.display = "none";
	// }
</script>



<!-- /.box -->

<!-- /.col -->

<!-- /.col -->

<!-- /.row -->
</section>