<div class="right-side" style="min-height: 413px;">


<section class="content " >
		<!-- Main row -->
		<div >
			<!-- Left col -->
			<div >
				<!-- MAP & BOX PANE -->
				<div class="box" style="display: none">
					<!-- <div class="box" > -->
					<div class="box-header box-header-background with-border">
						<h3 class="box-title">Sales Report</h3>

					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="row">
							<div class="col-md-10 col-sm-8">
								<p class="text-center">
									<strong>Sales: 1 Jan, - 31 Dec, </strong>
								</p>

								<div class="chart-responsive">
									<!-- Sales Chart Canvas -->
									<canvas width="860" style="width: 860px; height: 190px;" id="salesChart" height="300"></canvas>
								</div>
								<!-- /.chart-responsive -->
							</div>
							<!-- /.col -->
							<div class="col-md-2 col-sm-4">
								<!-- <div class="pad box-pane-right bg-green" style="min-height: 280px"> -->
								<div class="pad box-pane-right bg-green">

									<div class="description-block margin-bottom">
										<div class="sparkbar pad" data-color="#fff">
											<canvas height="30" width="34" style="display: inline-block; width: 34px; height: 30px; vertical-align: top;"></canvas>
										</div>
										<h5 class="description-header">100</h5>
										<span class="description-text">TOTAL REVENUE</span>
									</div>
									<!-- /.description-block -->
									<div class="description-block margin-bottom">
										<div class="sparkbar pad" data-color="#fff">
											<canvas height="30" width="34" style="display: inline-block; width: 34px; height: 30px; vertical-align: top;"></canvas>
										</div>
										<h5 class="description-header"><?= $currency . ' ' . number_format($total->buying_price, 2) ?></h5>
										<span class="description-text">TOTAL COST</span>
									</div>
									<!-- /.description-block -->
									<div class="description-block">
										<div class="sparkbar pad" data-color="#fff">
											<canvas height="30" width="34" style="display: inline-block; width: 34px; height: 30px; vertical-align: top;"></canvas>
										</div>
										<h5 class="description-header"><?= $currency . ' ' . number_format($total->product_tax, 2) ?></h5>
										<span class="description-text">TOTAL TAX</span>
									</div>
									<!-- /.description-block -->
								</div>
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.box-body -->
				</div>