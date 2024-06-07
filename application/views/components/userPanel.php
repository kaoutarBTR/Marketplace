<aside class="main-sidebar " style="height: 100%; position: absolute;" >

	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel">
			<div class="pull-left image">
				<?php
				$imageUrl='http://localhost/prestashop_/img/e/'.$this->session->userdata('id_employee').'.jpg';
				$imageInfo = @getimagesize($imageUrl);
				if (!$imageInfo) {
					// image does not exist 
					$imageUrl=base_url().'img/user.jpg';
				}
				// Image exists

				 ?>
				<img class="rounded-circle" src="<?php echo $imageUrl ?>" alt="photo de profil">

			</div>

			<!-- <div class="pull-left info" style="padding-top: 15px"> -->
			<div class="pull-left info">

				<p><?php print_r($this->session->userdata('name')) ?></p>
			</div>
		</div>
		<!-- Menu? -->
		<ul id="menu" class='treeview-menu' style="list-style-type: none;">
			<li class="menu-item" onclick="$this.classList.add('active')">
				<a href="<?php echo base_url(); ?>dashboard" class="trev-item" >
					<i class="fa-solid fa-gauge"></i>

					<span> Tableau de bord</span>
				</a>
			</li>
			<li class="menu-item" onclick="$this.classList.add('active')">
				<a href="<?php echo base_url(); ?>orders" class="trev-item">
					<!-- <i class="fa-solid fa-gear"></i> -->
					<i class="fa-solid fa-cart-shopping"></i>

					<span>Gestion des commandes</span>
				</a>
			</li>

			<!-- <li>
				<a href="<?php echo base_url(); ?>add_order" class="trev-item">
					<i class="fa-solid fa-cart-plus"></i>

					<span> Ajouter une commande</span>
					<i class='fa fa-angle-left '></i>
				</a>
			</li> -->

			<!-- <li>
				<a href="<?php echo base_url(); ?>clients" class="trev-item">
					<i class="fa-solid fa-user"></i>

					<span>Gestion des clients</span>
					<i class='fa fa-angle-left '></i>
				</a>
			</li> -->

			<!-- <li>
				<a href="<?php echo base_url(); ?>add_client" class="trev-item">

					<i class="fa-solid fa-user-plus"></i>

					<span> Ajouter un client</span>
					<i class='fa fa-angle-left '></i>
				</a>
			</li> -->

			<li class="menu-item" onclick="$this.classList.add('active')">
				<a href="<?php echo base_url(); ?>products" class="trev-item">
					<i class="fa-solid fa-box"></i>

					<span> Gestion des produits</span>
				</a>
			</li>

			<li class="menu-item" onclick="$this.classList.add('active')">
				<a href="<?php echo base_url(); ?>add_product" class="trev-item">
					<i class="fa-solid fa-dolly"></i>

					<span> Ajouter des produits</span>
				</a>
			</li>

			<li class="menu-item" onclick="$this.classList.add('active')">
				<a href="<?php echo base_url(); ?>stock_product" class="trev-item">

				<i class="fa-solid fa-shop"></i>
					<span> Mise à jour de stock </span>
				</a>
			</li>


		</ul>

	</section>
</aside>