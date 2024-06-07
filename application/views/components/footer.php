</div>
<footer class="main-footer">
		<strong>Copyright &copy; <?= date("Y") ?> <span style="color: #005299">MY OUTLET STORE</span>.</strong> Tous droits
		réservés.
	</footer>

	<!-- scripts -->

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src=" <?php echo base_url('asset/js/jquery-1.10.2.min.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/jquery-ui-1.10.4.custom.js'); ?> "></script>
	<script src="<?php echo base_url('asset/js/jquery-loader.js'); ?> " type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/timepicker.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/bootstrap-datepicker.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/admin.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/chosen/chosen.jquery.js'); ?>"></script>
	<script src=" <?php echo base_url('asset/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/jquery.validate.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/app.js'); ?> " type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/form-validation.js'); ?> " type="text/javascript"></script>
	<script src=" <?php echo base_url('asset/js/additional-methods.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/jasny-bootstrap.min.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/bootstrap-datepicker.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/timepicker.js'); ?>"></script>
	<script src="<?php echo base_url('asset/js/plugins/dataTables/jquery.dataTables.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/plugins/dataTables/dataTables.bootstrap.js'); ?>" type="text/javascript"></script>

	<script>
		$(document).ready(function() {
			$('#dataTables-example').dataTable();
		});
	</script>

	<script src="<?php echo base_url('asset/js/chartjs/Chart.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo base_url('asset/js/chartjs/dashboard.js'); ?>" type="text/javascript"></script>
	<script>
function confirmDelete(event) {
    // Prevent the default action of the click event (i.e., following the link immediately)
    event.preventDefault();

    // Display the confirmation dialog
    if (confirm("Êtes-vous sûr de vouloir supprimer cette commande ?")) {
        // If user confirms, redirect to the delete URL
        window.location.href = event.target.href;
    }
}
</script>
	<script>
// 	document.addEventListener("DOMContentLoaded", function() {
//   const currentPageUrl = window.location.pathname; // Get the current page URL path
// console.log(currentPageUrl);
//   // Check each menu item's link URL and add 'active' class if it matches the current page URL
//   const menuItems = document.querySelectorAll(' ul li ');
//   menuItems.forEach(item => {
// 	// cont item_list = item.querySelector('li');
//     const link = item.querySelector('a');
//     const linkUrl = link.getAttribute('href');
//     if (currentPageUrl === linkUrl) {
//       item.classList.add('active');
//     }
//   });
// menuItems.forEach(function(item) {
//         item.addEventListener('click', function() {
//             // Remove 'active' class from all menu items
//             menuItems.forEach(function(el) {
//                 el.classList.remove('active');
//             });
//             // Add 'active' class to the clicked menu item
//             item.classList.add('active');
//         });
//     });
// });

</script>


<script>$(document).ready(function() {
    // Show modal if successMessage is not empty
    if ("<?= $successMessage ?>" != '') {
        $("#myModal").css("display", "block");

        // Close modal when clicking on close button
        $(".close").on("click", function() {
            $("#myModal").css("display", "none");
        });

        // Close modal when clicking outside the modal content
        $(window).on("click", function(event) {
            if (event.target === $("#myModal")[0]) {
                $("#myModal").css("display", "none");
            }
        });
    }
});
</script>

<script>
		var $data_loading = {
			autoCheck: 32,
			size: 32,
			bgColor: '#FFF',
			bgOpacity: '0.7',
			fontColor: '#000',
			title: 'Chargement...',
			isOnly: true
		};

		jQuery(document).ready(function() {
			jQuery(".chosen-select").chosen({
				no_results_text: "Oops, Aucun client trouvé !",
			});
		});
	</script>


	<script>
		$(function() {

			/* ChartJS
			 * -------
			 * Here we will create a few charts using ChartJS
			 */

			//-----------------------
			//- MONTHLY SALES CHART -
			//-----------------------

			// Get context with jQuery - using jQuery's .get() method.
			var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
			// This will get the first returned node in the jQuery collection.
			var salesChart = new Chart(salesChartCanvas);

			var salesChartData = {
				labels: [
					<?php foreach ($yearly_sales_report as $name => $v_result) :
						$month_name = date('F', strtotime($year . '-' . $name)); // get full name of month by date query
					?> "<?= $month_name; ?>", // echo the whole month of the year
					<?php endforeach; ?>
				],
				datasets: [{
					label: "My Second dataset",
					fillColor: "rgba(151,187,205,0.2)",
					strokeColor: "rgba(151,187,205,1)",
					pointColor: "rgba(151,187,205,1)",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(151,187,205,1)",
					data: [

						<?php
						foreach ($yearly_sales_report as $v_result) :
						?> "<?php
							if (!empty($v_result)) {

								foreach ($v_result as $result) {

									echo round($result->grand_total);
								}
							}
							?>",
						<?php endforeach; ?>

					]
				}]
			};

			var options = {
				animation: false,


				scaleLabel: function(label) {
					return ' $' + label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				}

			};

			var salesChartOptions = {
				//Boolean - If we should show the scale at all
				showScale: true,
				//Boolean - Whether grid lines are shown across the chart
				scaleShowGridLines: true,
				//String - Colour of the grid lines
				scaleGridLineColor: "rgba(0,0,0,.05)",
				//Number - Width of the grid lines
				scaleGridLineWidth: 1,
				//Boolean - Whether to show horizontal lines (except X axis)
				scaleShowHorizontalLines: true,
				//Boolean - Whether to show vertical lines (except Y axis)
				scaleShowVerticalLines: true,
				//Boolean - Whether the line is curved between points
				bezierCurve: true,
				//Number - Tension of the bezier curve between points
				bezierCurveTension: 0.4,
				//Boolean - Whether to show a dot for each point
				pointDot: true,
				//Number - Radius of each point dot in pixels
				pointDotRadius: 4,
				//Number - Pixel width of point dot stroke
				pointDotStrokeWidth: 1,
				//Number - amount extra to add to the radius to cater for hit detection outside the drawn point
				pointHitDetectionRadius: 20,
				//Boolean - Whether to show a stroke for datasets
				datasetStroke: true,
				//Number - Pixel width of dataset stroke
				datasetStrokeWidth: 2,
				//Boolean - Whether to fill the dataset with a color
				datasetFill: true,

				// String - Template string for single tooltips
				tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= addCommas(value) %>",

				maintainAspectRatio: false,
				//Boolean - whether to make the chart responsive to window resizing
				responsive: true,

				scaleLabel: function(label) {
					return ' <?= $currency ?>' + label.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				}
			};

			//Create the line chart
			salesChart.Line(salesChartData, salesChartOptions);

			//---------------------------
			//- END MONTHLY SALES CHART -
			//---------------------------
		});

		function addCommas(nStr) {
			nStr += '';
			x = nStr.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
	</script>


</body>

</html>