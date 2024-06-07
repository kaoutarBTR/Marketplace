<div class="row">
	<!-- Left col -->
	<div class="col-md-9">
		<!-- TABLE: LATEST ORDERS -->
		<div class="box">

			<div class="box-header box-header-background with-border">

				<h3 class="box-title">Dernières commandes

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
									<th>Représentant</th>
									<th>Date de livraison prévue</th>
									<th>Montant</th>
									<th>Statut</th>
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
									echo '</td>                                           
                                            <td class="vertical-td">';
									echo '</td>
                                            <td class="vertical-td">';

									echo $ordersTable['delivery_date'];
									echo '</td>
                                            <td class="vertical-td">';
									echo $ordersTable['total'];
									echo '</td>
                                            <td class="vertical-td">';
									$colorClass = '';

									switch ($ordersTable['id_statut']) {
										case 6:
										case 8:
											$colorClass = ' label label-danger'; // Rouge
											break;
										case 2:
										case 5:
										case 7:
										case 11:
											$colorClass = ' label label-success'; // Vert
											break;
										default:
											$colorClass = 'label label-primary'; // Bleu par défaut
											break;
									}

									echo "<strong class='$colorClass'>" .  $ordersTable['statut'] . "</strong>";


									echo '</td>';


								?>


									</tr>
								<?php
								}
								?>

							</tbody>
						</table>

					<?php
					} else echo '<div class="box-footer text-center">
								Aucune commande à afficher

								</div>';
					?>
				</div>
				<!-- /.table-responsive -->
			</div>

			<!-- /.box-body -->
			<div class="box-footer clearfix">
				<a href="orders" class="btn btn-sm bg-purple btn-flat pull-right">Voir toutes les Commandes</a>
			</div>
			<!-- /.box-footer -->
		</div>
		<!-- /.box -->

		<!-- PRODUCT LIST -->
		<div class="box box-primary">
			<div class="box-header box-header-background with-border">
				<h3 class="box-title">Produits récemment ajoutés</h3>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<ul class="products-list product-list-in-box">

					<?php
					if ($productsTab != null) {
						foreach ($productsTab as $productsTable) {
					?>
							<li class="item">
								<div class="product-img">

									<img src="<?= $productsTable['image_link']?>" class="img-circle" alt="Product Image" />


								</div>
								<div class="product-info">
									<a href="<?= base_url() ?>admin/product/manage_product" class="product-title">
										Ref #:<?php echo $productsTable['reference'] ?></a>
									<span class="product-description">
										<?php echo $productsTable['description']; ?>
										<!-- //coloris,ligne,marque,	 -->
									</span>
								</div>
							</li><!-- /.item -->
						<?php
						}
					} else {

						?>
						<strong>Actuellement, il n'y a aucun produit à afficher </strong>
					<?php } ?>

				</ul>
			</div>

			<!-- /.box-body -->
			<div class="box-footer text-center">
				<a href="products" class="uppercase">Voir tous les
					produits</a>

			</div>
			<!-- /.box-footer -->
		</div>
		<!-- /.box -->
	</div>

	<!-- /.col -->
	<div class="col-md-3">
		<!-- Info Boxes Style 2 -->
		<div class="info-box bg-yellow">
			<div class="info-box">
				<span class="info-box-icon bg-aqua"><i class="glyphicon glyphicon-qrcode"></i></span>

				<div class="info-box-content box-color">
					<span class="info-box-text">TOTAL PRODUITS</span>
					<span class="info-box-number">
						<?php echo $numProducts ?>
					</span>
					<a href="/ProductController/gerer" class="small-box-footer">Plus
						d'infos <i class="fa fa-arrow-circle-right"></i></a>
				</div>
				<!-- /.info-box-content -->
			</div>
			<!-- /.info-box -->
		</div>
		<!-- /.info-box -->
		<div class="info-box bg-green">
			<div class="info-box">
				<span class="info-box-icon bg-purple"><i class="glyphicon glyphicon-shopping-cart"></i></span>

				<div class="info-box-content box-color">
					<span class="info-box-text">TOTAL COMMANDES</span>
					<span class="info-box-number">
						<?php echo $numOrders ?>
					</span>
					<a href="orders" class="small-box-footer">Plus d'infos
						<i class="fa fa-arrow-circle-right"></i></a>
				</div>
				<!-- /.info-box-content -->
			</div>
			<!-- /.info-box -->
		</div>

	</div>
	<!-- /.col -->
</div>

</div>
<!-- /.row -->
</section>